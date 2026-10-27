from flask import Flask, request, jsonify
from flask_cors import CORS
from main_agent import AgentInterface
import os

app = Flask(__name__)
CORS(app)  

print("Initializing AI Agent... please wait.")

agent = AgentInterface()
print("AI Agent Ready!")

@app.route('/chat', methods=['POST'])
def chat():
    try:
        data = request.json
        user_message = data.get('message', '')
        
        if not user_message:
            return jsonify({"error": "No message provided"}), 400

        response_text, metadata = agent.run_query(user_message)
        
        return jsonify({
            "response": response_text,
            "status": "success"
        })

    except Exception as e:
        print(f"Error: {e}")
        return jsonify({"error": str(e), "status": "error"}), 500

if __name__ == '__main__':
    print("Starting server on port 5000...")
    app.run(port=5000, debug=True)