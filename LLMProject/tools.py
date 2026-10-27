"""
tools.py - CHROMA VERSION
"""
import os
from typing import List, Dict, Any
from pathlib import Path

from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_google_genai import GoogleGenerativeAIEmbeddings
from langchain_community.vectorstores import Chroma

try:
    import PyPDF2
except:
    PyPDF2 = None

VECTORSTORE_DIR = "./vectorstore"


class Toolset:
    def __init__(self):
        self.tools = {}
        self.register("ingest", ingest_documents)

    def register(self, name: str, fn):
        self.tools[name] = fn

    def get_tool(self, name: str):
        return self.tools.get(name)


def extract_text(path: str) -> str:
    p = Path(path)

    if p.suffix.lower() == ".pdf" and PyPDF2:
        text = ""
        with open(p, "rb") as f:
            reader = PyPDF2.PdfReader(f)
            for page in reader.pages:
                text += page.extract_text() or ""
        return text

    return p.read_text(encoding="utf-8", errors="ignore")


def ingest_documents(file_paths: List[str]) -> Dict[str, Any]:
    embeddings = GoogleGenerativeAIEmbeddings(
        model="models/gemini-embedding-001"
    )

    splitter = RecursiveCharacterTextSplitter(
        chunk_size=1200,
        chunk_overlap=200
    )

    texts = []
    metadatas = []

    for path in file_paths:
        try:
            text = extract_text(path)

            chunks = splitter.split_text(text)

            texts.extend(chunks)
            metadatas.extend([{"source": os.path.basename(path)}] * len(chunks))

        except Exception as e:
            return {"status": "error", "error": str(e)}

    if not texts:
        return {"status": "error", "error": "No text found"}

    os.makedirs(VECTORSTORE_DIR, exist_ok=True)

    db = Chroma(
        persist_directory=VECTORSTORE_DIR,
        embedding_function=embeddings
    )

    db.add_texts(texts, metadatas=metadatas)
    db.persist()

    return {"status": "ok", "chunks": len(texts)}