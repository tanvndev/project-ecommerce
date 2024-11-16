import os
import sys
import pandas as pd
import torch
import torchvision.transforms as transforms
from torchvision.models import resnet50, ResNet50_Weights, efficientnet_b0, EfficientNet_B0_Weights
from PIL import Image, ImageOps
import numpy as np

# Kiểm tra tham số truyền vào từ dòng lệnh
if len(sys.argv) < 2:
    print("Image path is missing")
    sys.exit(1)

# Đường dẫn ảnh mới cần thêm
new_image_path = sys.argv[1]

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

# Trích xuất vector đặc trưng từ ảnh mới
resnet_features = extract_features(new_image_path, resnet_model, transform)
efficientnet_features = extract_features(new_image_path, efficientnet_model, transform)

# Kết hợp vector đặc trưng từ ResNet50 và EfficientNet-B0
combined_features = np.hstack((resnet_features, efficientnet_features))

# Đường dẫn file CSV
csv_path = 'image_features.csv'

# Kiểm tra nếu file CSV tồn tại, nếu không thì tạo file mới
if not os.path.exists(csv_path):
    # Tạo DataFrame mới và lưu vào file CSV
    df = pd.DataFrame(columns=['file_name', 'vector_resnet', 'vector_efficientnet'])
    df.to_csv(csv_path, index=False)

# Đọc file CSV hiện có
df = pd.read_csv(csv_path)

# Chuyển vector thành chuỗi
vector_resnet_str = ','.join(map(str, resnet_features))
vector_efficientnet_str = ','.join(map(str, efficientnet_features))

# Tên file ảnh
file_name = os.path.basename(new_image_path)

# Thêm dữ liệu mới vào DataFrame
new_data = pd.DataFrame({
    'file_name': [file_name],
    'vector_resnet': [vector_resnet_str],
    'vector_efficientnet': [vector_efficientnet_str]
})

# Cập nhật DataFrame và lưu vào file CSV
df = pd.concat([df, new_data], ignore_index=True)
df.to_csv(csv_path, index=False)

print(f"Successfully added features of {file_name} to {csv_path}")
