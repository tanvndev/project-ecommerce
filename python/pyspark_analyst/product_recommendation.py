from pyspark.sql import SparkSession

# Đường dẫn đến file JAR của MySQL JDBC driver
jdbc_driver_path = "/usr/share/java/mysql-connector-java-9.1.0.jar"  # Đảm bảo đường dẫn chính xác

# Khởi tạo SparkSession và chỉ định đường dẫn đến JDBC driver
spark = SparkSession.builder \
    .appName("User Behavior Analysis Ecommerce") \
    .config("spark.driver.bindAddress", "127.0.0.1") \
    .config("spark.driver.host", "127.0.0.1") \
    .config("spark.driver.port", "4042") \
    .config("spark.jars", jdbc_driver_path) \
    .getOrCreate()


# Thông tin kết nối với MySQL
jdbc_url = "jdbc:mysql://127.0.0.1:3306/magento_ecommerce"
properties = {
    "user": "tanvn",
    "password": "123456",
    "driver": "com.mysql.cj.jdbc.Driver"
}

# Đọc dữ liệu từ bảng "users"
df = spark.read.jdbc(url=jdbc_url, table="users", properties=properties)

# Hiển thị dữ liệu
df.show()
