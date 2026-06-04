# services/explanation_service.py

import random
import pandas as pd

FEATURE_DESCRIPTIONS = {
    "time_consistency_score": {
        "positive": [
            "waktu penyebaran berita ini terlihat tidak wajar (misalnya mendaur ulang berita lama)",
            "berita ini muncul di waktu yang tidak sinkron dengan kejadian aslinya",
            "tanggal publikasi informasi ini sangat acak dan mencurigakan"
        ],
        "negative": [
            "berita ini dirilis pada waktu yang wajar dan sesuai dengan kejadian sebenarnya",
            "berita ini diliput secara serempak oleh berbagai media pada waktu yang tepat",
            "alur waktu kejadian dan pelaporan beritanya sangat masuk akal"
        ]
    },

    "message_similarity_score": {
        "positive": [
            "isi beritanya sangat berbeda dengan fakta yang dilaporkan media resmi (cenderung clickbait/menyesatkan)",
            "judul dan isi artikel terlihat dilebih-lebihkan atau tidak sesuai konteks asli",
            "narasi berita ini sulit ditemukan kesamaannya di sumber-sumber yang kredibel"
        ],
        "negative": [
            "isi beritanya sejalan dengan apa yang dilaporkan oleh media-media terpercaya",
            "penyampaian informasinya sesuai dengan fakta yang beredar luas",
            "judul dan isi berita tidak melebih-lebihkan fakta di lapangan"
        ]
    },

    "mean_entailment": {
        "positive": [
            "nyaris tidak ada sumber berita terpercaya yang membenarkan informasi ini",
            "sangat minim bukti kredibel yang bisa mendukung kebenaran klaim tersebut",
            "klaim di dalam berita tidak didukung oleh fakta-fakta dari media arus utama"
        ],
        "negative": [
            "banyak media dan sumber terpercaya yang membenarkan informasi ini",
            "kebenaran berita ini didukung oleh bukti dan liputan resmi yang kuat",
            "mayoritas sumber informasi sepakat bahwa kejadian ini memang benar terjadi"
        ]
    },

    "mean_neutral": {
        "positive": [
            "informasi yang beredar masih sangat menggantung dan belum ada kepastian",
            "banyak pihak yang sekadar membahas rumor ini tanpa bisa membuktikan kebenarannya",
            "konteks beritanya masih simpang siur dan membingungkan publik"
        ],
        "negative": [
            "penjelasan dari berbagai pihak sudah sangat jelas dan tidak membingungkan",
            "fakta-faktanya sudah dijabarkan dengan pasti tanpa ada informasi yang disembunyikan",
            "berita ini memberikan konteks yang utuh sehingga mudah dipahami kebenarannya"
        ]
    },

    "mean_contradiction": {
        "positive": [
            "banyak media terpercaya yang secara tegas membantah informasi ini",
            "klaim dalam berita ini bertolak belakang dengan fakta resmi yang ada di lapangan",
            "ditemukan banyak bukti kuat yang menyanggah kebenaran cerita ini"
        ],
        "negative": [
            "tidak ada media atau pihak berwenang yang membantah kejadian ini",
            "informasinya sama sekali tidak bertentangan dengan fakta yang sudah diketahui umum",
            "semua sumber resmi sejalan dan tidak ada yang menyanggah berita ini"
        ]
    },

    "std_entailment": {
        "positive": [
            "informasi yang beredar sangat simpang siur (ada media yang membenarkan, ada yang tidak)",
            "kesepakatan fakta antar media sangat minim dan saling tumpang tindih",
            "terjadi kebingungan informasi di mana setiap sumber memberikan versi yang berbeda-beda"
        ],
        "negative": [
            "semua sumber terpercaya sangat kompak dan memiliki suara yang sama terkait kebenaran berita ini",
            "tidak ada perpecahan pendapat di antara media-media resmi mengenai fakta ini",
            "berbagai sumber sepakat secara bulat mendukung kebenaran informasi tersebut"
        ]
    },

    "std_neutral": {
        "positive": [
            "tanggapan publik dan media terhadap isu ini sangat terbelah",
            "beberapa sumber masih ragu-ragu, sementara yang lain sudah mengambil kesimpulan sepihak",
            "tingkat kejelasan informasi di lapangan masih sangat fluktuatif"
        ],
        "negative": [
            "pembahasan mengenai isu ini sudah stabil dan tidak memicu kebingungan baru",
            "media-media menyampaikan fakta dengan tingkat kejelasan yang seragam",
            "fokus pemberitaan sudah terarah dan tidak lagi berupa tebak-tebakan"
        ]
    },

    "std_contradiction": {
        "positive": [
            "terdapat perdebatan dan saling bantah yang cukup panas dari berbagai sudut pandang",
            "klarifikasi yang muncul dari berbagai pihak justru saling bertentangan satu sama lain",
            "isu ini memicu banyak kontroversi dan sanggahan yang tidak berujung"
        ],
        "negative": [
            "tidak ada perdebatan atau kontroversi berarti terkait fakta pada berita ini",
            "bantahan yang muncul sangat sedikit dan polanya mudah dipatahkan",
            "semua pihak relatif tenang dan menerima fakta ini tanpa perdebatan"
        ]
    },

    "len_results": {
        "positive": [
            "sangat sedikit media terpercaya yang mau meliput isu ini (ciri khas kabar burung)",
            "informasi ini seolah berasal dari sumber anonim karena minimnya liputan resmi",
            "sistem kesulitan menemukan referensi pendukung yang kredibel di internet"
        ],
        "negative": [
            "topik ini secara luas diliput oleh berbagai media arus utama yang kredibel",
            "sistem menemukan banyak sekali sumber referensi berkualitas yang membahas isu ini",
            "jejak digital dan dokumentasi terkait berita ini sangat mudah ditemukan"
        ]
    }
}


def generate_summary(
    prediction: int,
    confidence: float,
    feature_vector: dict,
    explainer
) -> str:
    """
    Menghasilkan narasi penjelasan prediksi berdasarkan SHAP values 
    dan 5-tier classification thresholds.
    """

    # =========================
    # MODEL FEATURE ORDER
    # (HARUS SAMA DENGAN CATBOOST)
    # =========================
    MODEL_FEATURES = [
        "time",
        "message",
        "mean_entailment",
        "mean_neutral",
        "mean_contradiction",
        "std_entailment",
        "std_neutral",
        "std_contradiction",
        "len_results"
    ]

    # =========================
    # BUILD DATAFRAME 
    # =========================
    X = pd.DataFrame([[
        feature_vector["time_consistency_score"],
        feature_vector["message_similarity_score"],
        feature_vector["mean_entailment"],
        feature_vector["mean_neutral"],
        feature_vector["mean_contradiction"],
        feature_vector["std_entailment"],
        feature_vector["std_neutral"],
        feature_vector["std_contradiction"],
        feature_vector["len_results"],
    ]], columns=MODEL_FEATURES)

    # =========================
    # CALCULATE SHAP VALUES
    # =========================
    shap_values = explainer.shap_values(X)

    if isinstance(shap_values, list):
        shap_values = shap_values[1]

    row = shap_values[0]

    # =========================
    # PAIR FEATURE + IMPACT
    # =========================
    impacts = [
        {"feature": f, "impact": float(v)}
        for f, v in zip(X.columns, row)
    ]

    # Ambil 3 fitur dengan kontribusi (absolut) terbesar
    impacts.sort(key=lambda x: abs(x["impact"]), reverse=True)
    top_features = impacts[:3]

    # =========================
    # MAPPING TO DESCRIPTIONS
    # =========================
    FEATURE_MAP = {
        "time": "time_consistency_score",
        "message": "message_similarity_score",
        "mean_entailment": "mean_entailment",
        "mean_neutral": "mean_neutral",
        "mean_contradiction": "mean_contradiction",
        "std_entailment": "std_entailment",
        "std_neutral": "std_neutral",
        "std_contradiction": "std_contradiction",
        "len_results": "len_results",
    }

    reasons = []

    for item in top_features:
        feature = FEATURE_MAP.get(item["feature"])
        impact = item["impact"]

        if feature not in FEATURE_DESCRIPTIONS:
            continue

        # Positif mendorong ke arah Hoax, Negatif mendorong ke arah Fakta
        direction = "positive" if impact > 0 else "negative"

        reasons.append(
            random.choice(FEATURE_DESCRIPTIONS[feature][direction])
        )

    # Fallback safety jika SHAP error atau alasan kosong
    if len(reasons) < 2:
        reasons = [
            "pola informasi dari sumber pembanding",
            "konsistensi data yang dianalisis sistem"
        ]

    # Gabungkan alasan menjadi narasi yang mengalir
    if len(reasons) > 1:
        alasan_teks = f"{', '.join(reasons[:-1])}, serta {reasons[-1]}"
    else:
        alasan_teks = reasons[0]

    confidence_pct = round(confidence * 100)

    # =========================
    # 5-TIER CLASSIFICATION LOGIC
    # =========================

    # 1. ZONA AMBIGU (Confidence < 60%)
    if confidence < 0.60:
        return (
            "⚪ INFORMASI BELUM DAPAT DIPASTIKAN ⚪\n"
            f"Sistem belum dapat mengambil kesimpulan pasti (Tingkat keyakinan hanya {confidence_pct}%). "
            f"Analisis menunjukkan bahwa {alasan_teks}. "
            "Sangat disarankan untuk menunggu liputan tambahan dari media arus utama, karena informasi saat ini masih terlalu simpang siur."
        )

    if prediction == 1:
        # 2. HOAX KUAT (Confidence >= 80%)
        if confidence >= 0.80:
            return (
                "🔴 TERINDIKASI KUAT SEBAGAI HOAX 🔴\n"
                f"Sistem sangat yakin ({confidence_pct}%) bahwa konten ini menyesatkan atau bohong. "
                f"Hal ini dikarenakan {alasan_teks}. "
                "Saran kami: Jangan langsung membagikan informasi ini."
            )
        # 3. CENDERUNG HOAX (Confidence 60% - 79%)
        else:
            return (
                "🟠 INDIKASI DISINFORMASI (CENDERUNG HOAX) 🟠\n"
                f"Terdapat indikasi ({confidence_pct}%) bahwa konten ini mengandung pelintiran fakta atau disinformasi. "
                f"Sistem menemukan bahwa {alasan_teks}. "
                "Harap berhati-hati, klaim dalam berita ini patut diragukan kebenarannya."
            )

    else:
        # 4. FAKTA KUAT (Confidence >= 80%)
        if confidence >= 0.80:
            return (
                "🟢 INFORMASI VALID 🟢\n"
                f"Sistem sangat yakin ({confidence_pct}%) bahwa konten ini faktual dan aman. "
                f"Hal ini didukung oleh temuan bahwa {alasan_teks}. "
                "Anda dapat mempercayai informasi ini karena sejalan dengan bukti dari berbagai referensi kredibel."
            )
        # 5. CENDERUNG FAKTA (Confidence 60% - 79%)
        else:
            return (
                "🟡 CENDERUNG FAKTA (MEMBUTUHKAN KONTEKS) 🟡\n"
                f"Informasi ini kemungkinan besar benar ({confidence_pct}%), namun sistem mendeteksi beberapa anomali. "
                f"Temuan menunjukkan bahwa {alasan_teks}. "
                "Secara umum inti beritanya aman, namun periksa kembali detail spesifiknya karena mungkin ada konteks yang terlewat."
            )