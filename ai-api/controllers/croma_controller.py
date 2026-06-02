from services.chroma_service import delete_from_chroma


def delete_from_chroma_controller(text_request_collection, data):
    ids = data.get("id") or data.get("ids") or data.get("query")

    if ids is None:
        return {
            "status": "error",
            "message": "Missing 'id', 'ids', or 'query' field"
        }

    if isinstance(ids, list):
        ids = [str(i) for i in ids]
    else:
        ids = [str(ids)]

    print(f"🗑️ Menghapus text_request IDs: {ids}")

    try:
        result = delete_from_chroma(text_request_collection, ids)
        print(f"✅ Berhasil menghapus text_request IDs: {result.get('deleted_ids', ids)}")
        return {
            "status": "success",
            "message": f"Deleted {len(ids)} item(s) from text_request",
            "deleted_ids": result.get("deleted_ids", ids)
        }
    except Exception as e:
        return {
            "status": "error",
            "message": str(e)
        }
