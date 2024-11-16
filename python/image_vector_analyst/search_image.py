import os
import pandas as pd
import torch
import torchvision.transforms as transforms
from torchvision.models import resnet50, ResNet50_Weights, efficientnet_b0, EfficientNet_B0_Weights
from PIL import Image, ImageOps
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.decomposition import PCA
import faiss

# Hàm cân bằng sáng cho ảnh
def histogram_equalization(image):
    return ImageOps.equalize(image)

# Hàm trích xuất vector đặc trưng từ mô hình
def extract_features(image_path, model, transform):
    image = Image.open(image_path).convert('RGB')
    image = histogram_equalization(image)
    image = transform(image).unsqueeze(0)
    with torch.no_grad():
        features = model(image)
    return np.round(features.numpy().flatten(), 10)

# Thiết lập mô hình ResNet50 và EfficientNet-B0
resnet_model = resnet50(weights=ResNet50_Weights.DEFAULT)
efficientnet_model = efficientnet_b0(weights=EfficientNet_B0_Weights.DEFAULT)
resnet_model.eval()
efficientnet_model.eval()

# Biến đổi ảnh
transform = transforms.Compose([
    transforms.Resize((299, 299)),
    transforms.ToTensor(),
    transforms.Normalize(mean=[0.485, 0.456, 0.406], std=[0.229, 0.224, 0.225])
])

# Đọc vector đặc trưng và tên ảnh từ file CSV
df = pd.read_csv('image_features.csv')

# Chuyển cột 'vector' từ chuỗi thành mảng numpy
feature_list_resnet = np.array([np.fromstring(vec, sep=',') for vec in df['vector_resnet']])
feature_list_efficientnet = np.array([np.fromstring(vec, sep=',') for vec in df['vector_efficientnet']])
image_paths = df['file_name'].values

# Kết hợp vector đặc trưng từ ResNet50 và EfficientNet-B0
combined_feature_list = np.hstack((feature_list_resnet, feature_list_efficientnet))

# Xác định số lượng thành phần chính cho PCA
n_samples, n_features = combined_feature_list.shape
n_components = min(100, n_samples, n_features)

# Giảm chiều vector đặc trưng bằng PCA
pca = PCA(n_components=n_components)
reduced_feature_list = pca.fit_transform(combined_feature_list)

# Sử dụng FAISS để tối ưu hóa tìm kiếm
index = faiss.IndexFlatL2(reduced_feature_list.shape[1])
index.add(reduced_feature_list.astype(np.float32))

# Ảnh cần so sánh
query_image_path = "iphone1.png"
query_features_resnet = extract_features(query_image_path, resnet_model, transform)
query_features_efficientnet = extract_features(query_image_path, efficientnet_model, transform)

# Kết hợp vector đặc trưng từ ResNet50 và EfficientNet-B0
combined_query_features = np.hstack((query_features_resnet, query_features_efficientnet))

# Giảm chiều vector truy vấn bằng PCA
query_features_reduced = pca.transform([combined_query_features])

# Tìm kiếm top N ảnh tương tự
top_n = 5
D, I = index.search(query_features_reduced.astype(np.float32), top_n)

# In kết quả
print(f"Top {top_n} similar images:")
for i in range(top_n):
    print(f"Image: {image_paths[I[0][i]]}, Distance: {D[0][i]:.4f}")
