import torch
import torchvision.transforms as transforms
from torchvision.models import resnet50, ResNet50_Weights, efficientnet_b0, EfficientNet_B0_Weights
from PIL import Image, ImageOps
import numpy as np
import os
import pandas as pd

# Hàm cân bằng sáng cho ảnh
def histogram_equalization(image):
    return ImageOps.equalize(image)

# Hàm trích xuất vector đặc trưng từ ảnh
def extract_features(image_path, model, transform):
    try:
        image = Image.open(image_path).convert('RGB')
        image = histogram_equalization(image)
        image = transform(image).unsqueeze(0)
        with torch.no_grad():
            features = model(image)
        features_rounded = np.round(features.numpy().flatten(), 10)
        return features_rounded
    except Exception as e:
        print(f"Error processing {image_path}: {e}")
        return None

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

# Thư mục chứa ảnh
image_folder = "../../laravel/storage/app/public/uploads/photos/2024"
feature_list_resnet = []
feature_list_efficientnet = []
image_names = []

# Duyệt toàn bộ các thư mục con và lấy ra các ảnh
for root, dirs, files in os.walk(image_folder):
    for filename in files:
        if filename.lower().endswith(('.png', '.jpg', '.jpeg', '.webp')):
            image_path = os.path.join(root, filename)
            
            # Trích xuất đặc trưng từ ResNet50
            features_resnet = extract_features(image_path, resnet_model, transform)
            if features_resnet is None:
                continue

            # Trích xuất đặc trưng từ EfficientNet-B0
            features_efficientnet = extract_features(image_path, efficientnet_model, transform)
            if features_efficientnet is None:
                continue

            # Lưu vector đặc trưng và đường dẫn ảnh
            feature_list_resnet.append(features_resnet)
            feature_list_efficientnet.append(features_efficientnet)
            image_names.append(filename)

print(f"Tìm thấy {len(image_names)} ảnh.")

# Tạo DataFrame với tên file và vector đặc trưng
df = pd.DataFrame({
    'file_name': image_names,
    'vector_resnet': [','.join(map(str, feature)) for feature in feature_list_resnet],
    'vector_efficientnet': [','.join(map(str, feature)) for feature in feature_list_efficientnet]
})

# Lưu vào file CSV
df.to_csv('image_features.csv', index=False)
print("Feature extraction completed and saved to 'image_features.csv'")
