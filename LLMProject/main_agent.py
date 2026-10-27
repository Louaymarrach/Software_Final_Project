import os
from typing import Any, Dict, Tuple
from dotenv import load_dotenv

load_dotenv()

VECTORSTORE_DIR = os.path.join(os.path.dirname(__file__), "vectorstore")

from langchain_google_genai import ChatGoogleGenerativeAI, GoogleGenerativeAIEmbeddings
from langchain_community.vectorstores import Chroma


class AgentInterface:
    def __init__(self, model_name="gemini-2.5-flash-lite"):
        self.llm = ChatGoogleGenerativeAI(
            model=model_name,
            temperature=0.2
        )

        self.vectorstore = None
        self.retriever = None

        self._load_vectorstore()

    def _load_vectorstore(self):
        print("\n Loading vectorstore...")

        embeddings = GoogleGenerativeAIEmbeddings(
            model="models/gemini-embedding-001"
        )

        self.vectorstore = Chroma(
            persist_directory=VECTORSTORE_DIR,
            embedding_function=embeddings
        )

        #  Better retrieval (MMR = more accurate)
        self.retriever = self.vectorstore.as_retriever(
            search_type="mmr",
            search_kwargs={"k": 8, "fetch_k": 20}
        )

        print(" Vectorstore ready")

    def run_query(self, query: str) -> Tuple[str, Dict[str, Any]]:
        metadata = {}

        clean_q = query.lower().strip()

        # =========================
        # SYSTEM BYPASS (FAST + FREE)
        # =========================
        if clean_q in ["hi", "hello", "hey"]:
            return "Hello! I am the UNDP AI Assistant. How can I help you?", {"method": "bypass"}

        if any(x in clean_q for x in ["who are you", "what are you", "introduce yourself"]):
            return (
                "I am the UNDP AI Assistant. I answer questions based on the system guidelines and documents you provided.",
                {"method": "bypass"}
            )

        if any(x in clean_q for x in ["what can you do", "help", "capabilities"]):
            return (
                "I can answer questions about volunteers, roles, workflows, and system rules based on the UNDP documentation.",
                {"method": "bypass"}
            )

        # =========================
        # 🔍 RAG PIPELINE
        # =========================
        print("\nQUERY:", query)

        try:
            docs = self.retriever.invoke(query)
        except Exception as e:
            print("RETRIEVER ERROR:", e)
            return "Error retrieving documents.", {"method": "error"}

        print("\n🔍 Retrieved docs:")

        if not docs:
            print("NONE")
            return "No relevant information found.", {"method": "no_docs"}

        # Combine context
        context = "\n\n".join([d.page_content for d in docs])

        print(context[:500])  # debug preview

        # ✅ Stronger prompt
        prompt = f"""
You are a UNDP assistant.

Answer the question using ONLY the context below.
If the answer is not clearly in the context, say: I don't know.

Context:
{context}

Question:
{query}

Answer:
"""
        try:
            response = self.llm.invoke(prompt)
            answer = response.content.strip()

            if not answer:
                return "I don't know.", {"method": "empty"}

            return answer, {"method": "RAG"}

        except Exception as e:
            print("LLM ERROR:", e)
            return "System busy. Please try again in a few seconds.", {"method": "quota_error"}