from pyspark.sql import SparkSession
from pyspark.sql.functions import col, count, explode, current_timestamp
from pyspark.ml.recommendation import ALS
from pyspark.sql import functions as F

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
db_user = "tanvn"
db_password = "123456"
jdbc_url = "jdbc:mysql://127.0.0.1:3306/magento_ecommerce"
properties = {
    "user": db_user,
    "password": db_password,
    "driver": "com.mysql.cj.jdbc.Driver"
}

# Đọc dữ liệu từ các bảng
product_views_df = spark.read.jdbc(url=jdbc_url, table="product_views", properties=properties)
cart_actions_df = spark.read.jdbc(url=jdbc_url, table="cart_actions", properties=properties)
orders_df = spark.read.jdbc(url=jdbc_url, table="orders", properties=properties)
order_items_df = spark.read.jdbc(url=jdbc_url, table="order_items", properties=properties)

# Gán điểm cho từng hành động của người dùng
view_score = 1
add_to_cart_score = 3
purchase_score = 5

# Tính điểm cho các hoạt động xem sản phẩm
views = product_views_df.groupBy("user_id", "product_variant_id") \
    .agg(F.count("*").alias("view_count")) \
    .withColumn("score", F.col("view_count") * view_score) \
    .filter(F.col("user_id").isNotNull())

# Tính điểm cho các hoạt động thêm vào giỏ hàng
add_to_cart = cart_actions_df.filter(F.col("action") == "added") \
    .groupBy("user_id", "product_variant_id") \
    .agg(F.count("*").alias("add_count")) \
    .withColumn("score", F.col("add_count") * add_to_cart_score) \
    .filter(F.col("user_id").isNotNull())

# Tính điểm cho các hoạt động mua hàng
completed_order_items_df = order_items_df.join(orders_df, order_items_df.order_id == orders_df.id) \
    .filter(orders_df.order_status == "completed")
purchases = completed_order_items_df.groupBy("user_id", "product_variant_id") \
    .agg(F.count("*").alias("purchase_count")) \
    .withColumn("score", F.col("purchase_count") * purchase_score) \
    .filter(F.col("user_id").isNotNull())

# Hợp nhất các điểm
user_behavior = views.union(add_to_cart).union(purchases) \
    .groupBy("user_id", "product_variant_id") \
    .agg(F.sum("score").alias("total_score"))

# Hiển thị dữ liệu
# user_behavior.show()


# Chuẩn bị dữ liệu cho mô hình ALS
als = ALS(maxIter=10, regParam=0.01, userCol="user_id", itemCol="product_variant_id", ratingCol="total_score", coldStartStrategy="drop")
model = als.fit(user_behavior)

# Gợi ý sản phẩm cho người dùng cụ thể
user_recommendations = model.recommendForAllUsers(20)

# Phân tách cột `recommendations` thành nhiều dòng để dễ dàng lưu vào cơ sở dữ liệu
user_recommendations_exploded = user_recommendations \
    .withColumn("product_variant_id", explode(col("recommendations.product_variant_id"))) \
    .withColumn("total_score", explode(col("recommendations.rating"))) \
    .select("user_id", "product_variant_id", "total_score")

# Thêm cột created_at và updated_at
user_recommendations_exploded = user_recommendations_exploded \
    .withColumn("created_at", current_timestamp()) \
    .withColumn("updated_at", current_timestamp())

# Kiểm tra DataFrame đã sẵn sàng
# user_recommendations_exploded.show()

user_recommendations_exploded.write \
    .format("jdbc") \
    .option("url", jdbc_url) \
    .option("dbtable", "product_recommendations") \
    .option("user", db_user) \
    .option("password", db_password) \
    .option("driver", "com.mysql.cj.jdbc.Driver") \
    .mode("overwrite") \
    .save()

print('Save success!')
