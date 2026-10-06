from flask import Flask, render_template, request
from bot import visit


app = Flask(__name__)

@app.route('/')
def index():
    return render_template('index.html')

@app.route('/report', methods=['POST'])
def report():
    try:
        visit(request.form.get('url'))
    except Exception as e:
        return 'An error occurred while reporting the bug.'
    return 'Thank you for the great bug report!'
    

if __name__ == '__main__':
    # debug mode off for security
    app.run(debug=False, host='0.0.0.0', port=9090, passthrough_errors=True, threaded=False)