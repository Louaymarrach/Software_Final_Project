import os
import argparse
from dotenv import load_dotenv

load_dotenv()

api_key = os.getenv("GEMINI_API_KEY") or os.getenv("GOOGLE_API_KEY")
if not api_key:
    raise ValueError("No API key found.")

os.environ["GOOGLE_API_KEY"] = api_key

from langchain_community.document_loaders import TextLoader, PyPDFLoader
from langchain_text_splitters import RecursiveCharacterTextSplitter
from langchain_google_genai import GoogleGenerativeAIEmbeddings
from langchain_community.vectorstores import Chroma


def ingest(folders, output_dir: str = './vectorstore'):
    docs = []

    # LOAD FILES
    for docs_folder in folders:
        if not os.path.exists(docs_folder):
            print(f" Skipping {docs_folder}")
            continue

        for root, _, files in os.walk(docs_folder):
            for fname in files:
                path = os.path.join(root, fname)

                try:
                    if fname.lower().endswith('.txt'):
                        loader = TextLoader(path, encoding='utf-8')
                    elif fname.lower().endswith('.pdf'):
                        loader = PyPDFLoader(path)
                    else:
                        continue

                    docs.extend(loader.load())
                    print(f" Loaded: {fname}")

                except Exception as e:
                    print(f" {fname}: {e}")

    if not docs:
        print(" No documents found")
        return

    print(f"\n Loaded {len(docs)} documents")

    splitter = RecursiveCharacterTextSplitter(
        chunk_size=1000,
        chunk_overlap=200
    )

    chunks = splitter.split_documents(docs)
    print(f"🔹 {len(chunks)} chunks created")

    print("\n Generating embeddings...")

    embeddings = GoogleGenerativeAIEmbeddings(
        model="models/gemini-embedding-001"
    )

    # DELETE OLD DB (IMPORTANT)
    if os.path.exists(output_dir):
        import shutil
        shutil.rmtree(output_dir)

    print("💾 Saving to Chroma...")

    vectorstore = Chroma.from_documents(
        documents=chunks,
        embedding=embeddings,
        persist_directory=output_dir
    )

    print(f"\n✅ DONE: {len(chunks)} chunks stored")


if __name__ == '__main__':
    parser = argparse.ArgumentParser()
    parser.add_argument('--folders', nargs='+', default=['./data'])
    parser.add_argument('--output_dir', type=str, default='./vectorstore')
    args = parser.parse_args()

    ingest(args.folders, args.output_dir)