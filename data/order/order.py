import random
import requests
import time


token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvYXBpL3YxL2F1dGgvbG9naW4iLCJpYXQiOjE3MjkwMDAzMTQsImV4cCI6MTczMTU5MjMxNCwibmJmIjoxNzI5MDAwMzE0LCJqdGkiOiJsanNVTm4xMHVTN2xEOG1jIiwic3ViIjoiMSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.LMPWp0MHegTQf4hQQY4IrpM-2b45azP5954pqGRVyOY"


def call_api(i):
    headers = {"Authorization": f"Bearer {token}", "Content-Type": "application/json"}
    response = requests.post(
        "http://127.0.0.1:8001/api/v1/orders/create", headers=headers
    )
    if response.status_code == 201 or response.status_code == 200:
        print(f"API call successful! count: {i}")
    else:
        print(f"Request failed with status code: {response.status_code}")


for i in range(20000):
    call_api(i)
