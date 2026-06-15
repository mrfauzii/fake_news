# services/db_service.py
from config.db_config import get_connection
import pandas as pd
import json
from contextlib import closing
from datetime import datetime
from zoneinfo import ZoneInfo

now_wib = datetime.now(ZoneInfo("Asia/Jakarta"))
def get_latest_title():
    supabase = get_connection()

    res = supabase.table("knowledge_base") \
        .select("title") \
        .order("published_at", desc=True) \
        .limit(1) \
        .execute()

    data = res.data
    return data[0]["title"].lower() if data else ""

from datetime import datetime
from zoneinfo import ZoneInfo
import json

def insert_to_supabase(df):
    supabase = get_connection()

    list_id_chroma = []

    df = df.where(pd.notnull(df), None)

    now_wib = datetime.now(ZoneInfo("Asia/Jakarta"))

    for _, row in df.iterrows():

        teks_hoaks = row.get('klaim') or row.get('penjelasan')

        links = row.get('link_counter')
        if isinstance(links, str):
            try:
                links = json.loads(links)
            except:
                links = []

        if not isinstance(links, list):
            links = []

        payload = {
            "title": row.get('judul'),
            "source_url": row.get('link'),
            "category": row.get('kategori_hoaks'),
            "hoax_text": teks_hoaks,
            "fact_text": row.get('fakta'),
            "link_counter": links,
            "published_at": row.get('tanggal'),
            "created_at": str(now_wib),
            "updated_at": str(now_wib),
        }

        res = supabase.table("knowledge_base").insert(payload).execute()

        if res.data:
            kb_id = res.data[0]["id"]
            list_id_chroma.append(str(kb_id))

    print(f"✅ Berhasil insert {len(list_id_chroma)} data ke Supabase")
    return list_id_chroma

def get_row_by_id(id_):
    supabase = get_connection()

    res = supabase.table("knowledge_base") \
        .select("title, hoax_text, fact_text, category") \
        .eq("id", id_) \
        .single() \
        .execute()

    return res.data if res.data else {}