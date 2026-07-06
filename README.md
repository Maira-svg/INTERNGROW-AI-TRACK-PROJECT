# 🎵 AI Track – Pure PHP Version 

AI Track is a lightweight AI-powered web application built entirely in **PHP**. It does **not require Python**, machine learning frameworks, or additional runtime environments. If you already have **XAMPP** installed, you can run the project immediately.

## ✨ Features

### 🌍 1. AI Translation

* Translate text into multiple languages.
* Uses PHP cURL to connect to the free Google Translate endpoint.
* No Python libraries required.

### 🤖 2. AI FAQ Chatbot

* Built completely in PHP.
* Uses **TF-IDF (Term Frequency–Inverse Document Frequency)** and **Cosine Similarity** for retrieval.
* Works as a lightweight **RAG-style retrieval chatbot**.
* Not based on hardcoded `if/else` statements.

### 🎼 3. AI Music Generator

* Rule-based music suggestion system.
* Generates:

  * Chord Progressions
  * Melody Ideas
  * Music Style Suggestions
* Works completely offline.

---

# 🛠 Requirements

* PHP 8.x (included with XAMPP)
* Apache Server (XAMPP)
* Internet connection (only for Translation feature)

No Python installation is required.

---

# 📥 Installation

## Step 1 – Install XAMPP

If XAMPP is already installed, skip this step.

Download XAMPP:

https://www.apachefriends.org/download.html

---

## Step 2 – Copy the Project

Extract the project folder and place it inside:

```text
C:\xampp\htdocs\AI_Track_Project_PHP\
```

---

## Step 3 – Start Apache

Open **XAMPP Control Panel** and click:

```
Start → Apache
```

---

## Step 4 – Run the Project

Open your browser and visit:

```text
http://localhost/AI_Track_Project_PHP/index.php
```

The application will start immediately.

---

# 📁 Project Structure

```text
AI_Track_Project_PHP/
│
├── index.php
│      Main user interface and request handling
│
├── ai_functions.php
│      AI logic including:
│      - Translation
│      - FAQ Chatbot
│      - Music Generator
│
└── README.md
       Project documentation
```

---

# 🚀 How It Works

## 🌍 Translation

* Uses PHP cURL.
* Sends requests to Google's free translation endpoint.
* Requires an internet connection.

---

## 🤖 FAQ Chatbot

The chatbot performs semantic retrieval using:

* Text Tokenization
* TF-IDF Vectorization
* Cosine Similarity

### Workflow

1. User enters a question.
2. The question is tokenized.
3. A TF-IDF vector is generated.
4. Cosine similarity is calculated against stored FAQ questions.
5. The highest matching answer is returned.
6. If similarity is below the threshold, a fallback response is displayed.

This makes the chatbot retrieval-based rather than relying on hardcoded conditions.

---

## 🎵 Music Generator

The music generator is entirely rule-based.

It suggests:

* Chord Progressions
* Melody Patterns
* Musical Styles

based on the selected genre and mood.

This feature works completely offline.

---

# ⚙ Configuration

To customize the application, edit:

```text
ai_functions.php
```

You can modify:

* FAQ dataset
* Supported languages
* Music genres
* Mood presets
* Chord progressions
* Melody suggestions

---

# ⚠ Common Issues

## Translation API Failed

Possible causes:

* No internet connection
* Temporary rate limiting by the free Google Translate endpoint

Wait a few minutes and try again.

---

## Blank White Page

Enable PHP error reporting.

In `php.ini`:

```ini
display_errors = On
```

Restart Apache.

You can also check:

```text
C:\xampp\apache\logs\error.log
```

---

## 404 Not Found

Make sure the project folder is inside:

```text
C:\xampp\htdocs\
```

and access it using the correct URL.

---

## cURL Errors

Enable PHP cURL.

In `php.ini`:

```ini
extension=curl
```

Remove any leading `;`, save the file, and restart Apache.

---

# 💻 Technologies Used

* PHP
* HTML5
* CSS3
* JavaScript
* Apache (XAMPP)
* PHP cURL
* TF-IDF
* Cosine Similarity

---

# 📌 Key Highlights

* 100% PHP implementation
* No Python required
* No machine learning libraries
* Lightweight and easy to deploy
* Retrieval-based chatbot
* Offline music generator
* Simple project structure
* Beginner-friendly codebase

---

# 📄 License

This project is developed for educational and learning purposes. You are free to modify and extend it for personal or academic use.
Github link:
https://github.com/Maira-svg/INTERNGROW-AI-TRACK-PROJECT
# 🗣️ Guftagu — AI Chatbot (OpenRouter)

Guftagu ek simple aur elegant AI chatbot hai jo **OpenRouter API** ke zariye kaam karta hai. Yeh multiple AI models ko support karta hai, including Claude, GPT-4, Gemini, aur free open-source models.

![Guftagu Chatbot](https://img.shields.io/badge/version-1.0.0-blue)
![OpenRouter](https://img.shields.io/badge/OpenRouter-API-orange)
![License](https://img.shields.io/badge/license-MIT-green)

---

## ✨ Features

- 🤖 **Multiple AI Models** — Claude, GPT-4, Gemini, Llama, Mistral aur bhi
- 🔑 **OpenRouter Integration** — Ek hi API key se sab models
- 💬 **Real-time Chat** — Instant responses with typing indicator
- 🌙 **Dark Theme** — Eye-friendly design
- 📱 **Responsive** — Mobile aur desktop dono par kaam kare
- 💾 **Local Storage** — API key aur model preference save hoti hai
- 🧹 **Clear History** — Nayi guftagu shuru karne ka option

---


<img width="955" height="537" alt="image" src="https://github.com/user-attachments/assets/6d4f7b7b-4693-4508-b166-eb0f212e4330" />


---

## 🛠️ Technologies Used

- **HTML5** — Structure
- **CSS3** — Styling (Dark theme, responsive)
- **JavaScript (Vanilla)** — Logic and API calls
- **OpenRouter API** — AI model gateway
-

---

## ⭐ If you find this project helpful, consider giving it a Star on GitHub!
