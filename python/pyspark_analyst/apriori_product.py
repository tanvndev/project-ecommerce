from pyspark.sql import SparkSession
from pyspark.sql.functions import col, count, explode, current_timestamp, collect_list, concat_ws
from pyspark.ml.fpm import FPGrowth
from pyspark.sql import functions as F
import redis
import json
from decimal import Decimal

# Kết nối Redis
r = redis.Redis(host="127.0.0.1", port=6379, db=0)

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

orders_df = spark.read.jdbc(url=jdbc_url, table="orders", properties=properties)
order_items_df = spark.read.jdbc(url=jdbc_url, table="order_items", properties=properties)

# Lọc các đơn hàng đã hoàn thành
completed_orders_df = orders_df.filter((col("order_status") == "completed"))
completed_order_items_df = completed_orders_df.join(order_items_df, completed_orders_df.id == order_items_df.order_id)
# Nhóm các sản phẩm theo `order_id` và tạo danh sách `product_variant_id`
order_product_df = completed_order_items_df.groupBy("order_id").agg(
    F.collect_list("product_variant_id").alias("product_variant_ids")
)
order_product_df = order_product_df.filter(
    (F.col("product_variant_ids").isNotNull()) & (F.size("product_variant_ids") > 0)
)
# Loại bỏ các sản phẩm trùng lặp trong danh sách
order_product_df = order_product_df.withColumn(
    "product_variant_ids", F.array_distinct("product_variant_ids")
)

order_product_df.show(truncate=False)

# Lấy kết quả và chuyển các giá trị Decimal thành float
def convert_decimal_to_float(obj):
    """Hàm chuyển Decimal thành float trong dictionary hoặc list."""
    if isinstance(obj, Decimal):
        return float(obj)
    elif isinstance(obj, dict):
        return {key: convert_decimal_to_float(value) for key, value in obj.items()}
    elif isinstance(obj, list):
        return [convert_decimal_to_float(item) for item in obj]
    else:
        return obj

# Áp dụng thuật toán FPGrowth
fp_growth = FPGrowth(
    itemsCol="product_variant_ids",
    minSupport=0.005,
    minConfidence=0.01,
)
model = fp_growth.fit(order_product_df)

frequent_itemsets = model.freqItemsets
association_rules = model.associationRules

# Lấy kết quả từ frequent_itemsets và association_rules
frequent_itemsets_data = frequent_itemsets.collect()
association_rules_data = association_rules.collect()

# Lưu kết quả vào Redis
with r.pipeline() as pipe:

    # Xóa các key cũ trước khi lưu kết quả mới
    pipe.delete("laravel_database_apriori_frequent_itemsets")
    pipe.delete("laravel_database_apriori_association_rules")

    # Lưu các Frequent Itemsets
    for itemset in frequent_itemsets.collect():
        result_dict = {
            "items": itemset["items"],
            "support": itemset["freq"]
        }
        # Chuyển các giá trị Decimal thành float
        result_dict = convert_decimal_to_float(result_dict)
        json_result = json.dumps(result_dict)
        pipe.rpush("laravel_database_apriori_frequent_itemsets", json_result)

    # Lưu các Association Rules
    for rule in association_rules.collect():
        rule_dict = {
            "antecedent": rule["antecedent"],
            "consequent": rule["consequent"],
            "confidence": rule["confidence"],
            "lift": rule["lift"]
        }
        # Chuyển các giá trị Decimal thành float
        rule_dict = convert_decimal_to_float(rule_dict)
        json_rule = json.dumps(rule_dict)
        pipe.rpush("laravel_database_apriori_association_rules", json_rule)

    # Ghi kết quả vào Redis một lần
    pipe.execute()

print("Frequent Itemsets and Association Rules saved to Redis.")
