
from services.text_pipeline_service import process_fake_news_pipeline

async def detect_text_fake_news_controller(collection, transformer, nli,client,data,browser):

    if not data or "query" not in data:
        return {"error": "Query tidak ditemukan"}

    query = data["query"]

    result = await process_fake_news_pipeline(
        raw_text=query,
        collection=collection,
        transformer=transformer,
        nli=nli,
        client=client,
        browser=browser
    )
    return result