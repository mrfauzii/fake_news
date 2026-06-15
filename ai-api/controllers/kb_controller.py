from services.scraper_service import *
import pandas as pd
from services.db_service import insert_to_mysql, get_latest_title
from services.chroma_service import insert_to_chroma
import requests

def update_knowledge_base_controller(model, collection, data, batch_size=32,):
    token =data.get("token")
    url = "http://127.0.0.1:8000/scraper/done/" + token
    
    print("🚀 Memulai proses scraping data hoaks...")
    latest_title = get_latest_title()
    print(f"📌 Judul terbaru di database: '{latest_title}'")
    df = scrape_new_hoaxes(latest_title)
    df = scrape_all(df)
    if ((len(df) == 0) ):
        print("✅ knowledge base sudah paling baru")
        requests.get(url)
        return {
                    "status": "success",
                    "message": "knowledge base sudah paling baru"
                }
    df = retry_scrape_nan(df)
    df = clean_dataframe(df)
    print(df.columns)
    
    if df is None or df.empty:
        print("✅ Tidak ada data hoaks baru untuk diproses hari ini.")
        return None

    list_id = insert_to_mysql(df)
    insert_to_chroma(df, list_id, model, collection, batch_size)
    
    requests.get(url)

    return {
        "status": "success",
        "message": "Scraping dan sinkronisasi selesai"
    }
