const chat = document.getElementById("chat");
const input = document.getElementById("message");
const button = document.getElementById("send");

function addMessage(text, type) {
  const div = document.createElement("div");
  div.className = `message ${type}`;
  div.textContent = text;
  chat.appendChild(div);
  chat.scrollTop = chat.scrollHeight;
}

button.addEventListener("click", async () => {
  const message = input.value.trim();
  if (!message) return;

  addMessage(message, "user");
  input.value = "";
  button.disabled = true;

  const loading = document.createElement("div");
  loading.className = "message bot";
  loading.textContent = "Le bot écrit…";
  chat.appendChild(loading);
  chat.scrollTop = chat.scrollHeight;

  try {
    const response = await fetch("chat.php", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ message })
    });

    const data = await response.json();
    loading.remove();
    addMessage(data.reply, "bot");

  } catch (e) {
    loading.remove();
    addMessage("Une erreur est survenue.", "bot");
  }

  button.disabled = false;
});

input.addEventListener("keydown", e => {
  if (e.key === "Enter") {
    button.click();
  }
});


document.addEventListener('DOMContentLoaded', function() {
    var options = {
        strings: ["Bienvenue!", "Je suis Développeur Web.", "Découvrez mon portfolio."], // Les phrases qui défileront
        typeSpeed: 70,  // Vitesse de frappe (en ms)
        backSpeed: 50,  // Vitesse d'effacement (en ms)
        backDelay: 1500, // Délai avant l'effacement après la frappe complète (en ms)
        startDelay: 500, // Délai avant de commencer à taper
        loop: true // Répéter le cycle
    };

    var typed = new Typed('.typed-text', options);
});

