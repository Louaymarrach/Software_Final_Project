"""
app.py - Cleaned up Streamlit UI
"""
import os
import html
import streamlit as st
from dotenv import load_dotenv
from main_agent import AgentInterface

load_dotenv()
st.set_page_config(page_title="UN Chatbot", layout="wide")

st.title("UN AI Assistant")
st.markdown('<hr>', unsafe_allow_html=True)

os.makedirs("uploads", exist_ok=True)

with st.spinner("Initializing AI Agent..."):
    agent = AgentInterface()

col1, col2 = st.columns([2, 1])

# --- Left Column: Chat ---
with col1:
    st.header("Chat")
    if "chat_history" not in st.session_state:
        st.session_state.chat_history = []

    def send_message():
        user_input = st.session_state.input_text
        if not user_input.strip(): return
        response, meta = agent.run_query(user_input)
        st.session_state.chat_history.append({"role": "user", "message": user_input})
        st.session_state.chat_history.append({"role": "agent", "message": response, "meta": meta})
        st.session_state.input_text = ""

    for chat in st.session_state.chat_history:
        msg_safe = html.escape(chat["message"])
        if chat["role"] == "user":
            st.markdown(f"<div style='text-align:right; margin-bottom:10px;'><span style='background:#006eb6; color:white; padding:10px; border-radius:10px;'>{msg_safe}</span></div>", unsafe_allow_html=True)
        else:
            st.markdown(f"<div style='text-align:left; margin-bottom:10px;'><span style='background:#f1f5f9; padding:10px; border-radius:10px; display:inline-block;'>{msg_safe}</span></div>", unsafe_allow_html=True)
            if chat.get("meta"):
                st.caption(f"*Source: {chat['meta'].get('method', 'Unknown')}*")

    st.text_area("Ask a question about the UN:", key="input_text", height=100)
    st.button("Send", on_click=send_message)

# --- Right Column: Ingestion ---
with col2:
    st.header("Admin: Document Upload")
    st.write("Upload PDFs or Text files to teach the AI new information.")
    
    files = st.file_uploader("Upload files", accept_multiple_files=True, type=["pdf", "txt"])
    
    if st.button("Process & Ingest Files"):
        if files:
            saved_paths = []
            for f in files:
                save_path = os.path.join("uploads", f.name)
                with open(save_path, "wb") as fh:
                    fh.write(f.getbuffer())
                saved_paths.append(save_path)
            
            with st.spinner("Ingesting into AI brain..."):
                result = agent.ingest_tool(saved_paths)
                if result.get("status") == "ok":
                    st.success("Successfully ingested files for RAG!")
                else:
                    st.error(f"Error: {result.get('error')}")
        else:
            st.warning("Please upload files first.")