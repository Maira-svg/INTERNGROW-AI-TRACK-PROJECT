# AI Track — Pure PHP Version (No Python Needed)

Yeh version **sirf PHP** mein bana hai — Python install karne ki koi zarurat nahi.
Sab 3 AI features PHP ke andar hi kaam karte hain:

1. **Translation** — PHP curl se seedha Google Translate free endpoint call karta hai
2. **FAQ Chatbot** — Real TF-IDF + cosine similarity, pure PHP mein likha hua (RAG-style retrieval, hardcoded if/else nahi)
3. **AI Music Generator** — Rule-based chord progression / melody / style suggester

---

## 1. Kya install karna hai

Sirf **ek** cheez chahiye: **PHP** (jo XAMPP ke saath already aata hai).

Agar XAMPP already installed hai → **kuch install nahi karna**, seedha Step 2 pe jao.

Agar XAMPP nahi hai:
- Download: https://www.apachefriends.org/download.html
- Install kar lo (Apache + PHP dono aa jayenge)

---

## 2. Project ko htdocs mein daalna

1. Is zip ko extract karo
2. `AI_Track_Project_PHP` folder ko copy karke yahan paste karo:
   ```
   C:\xampp\htdocs\AI_Track_Project_PHP\
   ```

---

## 3. Run karna

1. **XAMPP Control Panel** kholo
2. **Apache** ke saamne **Start** button dabao (green ho jayega)
3. Browser me kholo:
   ```
   http://localhost/AI_Track_Project_PHP/index.php
   ```

**Bas itna hi.** Koi Python, koi `pip`, koi second terminal nahi chahiye.

---

## 4. Project structure

```
AI_Track_Project_PHP/
├── index.php          # UI + request handling (single entry point)
├── ai_functions.php   # Saara AI logic (translate, chatbot, music) — pure PHP
└── README.md          # Yeh file
```

---

## 5. Common issues

- **"Translation API failed"** → Internet connection check karo. Yeh feature Google ke free public endpoint ko call karta hai, isliye internet chahiye. Kabhi kabhi ye endpoint temporarily block/rate-limit kar deta hai — thodi der baad try karo.
- **Blank white page aaye** → PHP error display off ho sakta hai. `php.ini` me `display_errors = On` kar ke Apache restart karo, ya XAMPP error log (`C:\xampp\apache\logs\error.log`) check karo.
- **404 Not Found** → Path check karo, folder `htdocs` ke andar hi hona chahiye aur URL me exact folder ka naam match hona chahiye.
- **curl errors (translation)** → PHP ka `curl` extension enable hona chahiye. XAMPP me by default enabled hota hai; agar nahi hai to `php.ini` me `extension=curl` line se `;` hata do aur Apache restart karo.

---

## 6. Notes on the AI

- **Chatbot is genuinely doing retrieval** — jab aap sawal type karte ho, PHP us sawal ko tokenize karta hai, TF-IDF vector banata hai, aur 10 FAQ questions ke vectors se cosine similarity compare karta hai. Best match 25% se upar ho to wahi answer return hota hai — warna "I couldn't find a specific answer" wala fallback.
- FAQ list, languages, ya music genres/moods badalne ke liye `ai_functions.php` file edit karo — sab kuch ek hi file me hai, samajhna aasan hai.
- Translation ke liye internet chahiye; chatbot aur music generator dono **100% offline** kaam karte hain.
