# import pandas as pd
# from apyori import apriori
# import redis
# import json

# r = redis.Redis(host="127.0.0.1", port=6379, db=0)

# chunk_size = 10000
# transactions = []
# path_file = "../laravel/public/orders.csv"

# for chunk in pd.read_csv(path_file, chunksize=chunk_size):
#     if "product_variant_ids" not in chunk.columns:
#         print("Error: Column 'product_variant_ids' not found in CSV file")
#         exit(1)

#     chunk_transactions = (
#         chunk["product_variant_ids"].dropna().apply(lambda x: x.split(",")).tolist()
#     )
#     transactions.extend(chunk_transactions)


# results = list(
#     apriori(
#         transactions, min_support=0.001, min_confidence=0.01, min_lift=1.2, max_length=3
#     )
# )
# print(f"Number of rules found: {len(results)}")

# for result in results:
#     result_dict = {
#         "items": list(result.items),
#         "support": result.support,
#         "ordered_statistics": [
#             {
#                 "items_base": list(stat.items_base),
#                 "items_add": list(stat.items_add),
#                 "confidence": stat.confidence,
#                 "lift": stat.lift,
#             }
#             for stat in result.ordered_statistics
#         ],
#     }
#     # print(result_dict)
#     json_result = json.dumps(result_dict)
#     r.rpush("laravel_database_apriori_suggest_product", json_result)

# print("Apriori results saved to Redis.")

import pandas as pd
from apyori import apriori
import redis
import json

# Kết nối Redis
r = redis.Redis(host="127.0.0.1", port=6379, db=0)

chunk_size = 10000  # Có thể điều chỉnh chunk_size nếu cần
path_file = "../laravel/public/orders.csv"

# Đọc và xử lý từng chunk
for chunk in pd.read_csv(path_file, chunksize=chunk_size):
    if "product_variant_ids" not in chunk.columns:
        print("Error: Column 'product_variant_ids' not found in CSV file")
        exit(1)

    # Xử lý chunk để tạo các giao dịch
    transactions = (
        chunk["product_variant_ids"].dropna().apply(lambda x: x.split(",")).tolist()
    )

    # Áp dụng thuật toán Apriori cho từng chunk
    results = list(
        apriori(
            transactions,
            min_support=0.01,
            min_confidence=0.01,
            min_lift=1.2,
            max_length=3,
        )
    )
    print(f"Number of rules found in current chunk: {len(results)}")

    # Sử dụng Redis pipeline để lưu kết quả
    with r.pipeline() as pipe:
        for result in results:
            result_dict = {
                "items": list(result.items),
                "support": result.support,
                "ordered_statistics": [
                    {
                        "items_base": list(stat.items_base),
                        "items_add": list(stat.items_add),
                        "confidence": stat.confidence,
                        "lift": stat.lift,
                    }
                    for stat in result.ordered_statistics
                ],
            }
            json_result = json.dumps(result_dict)
            pipe.rpush("laravel_database_apriori_suggest_product", json_result)

        # Ghi kết quả vào Redis một lần
        pipe.execute()

print("Apriori results saved to Redis.")
