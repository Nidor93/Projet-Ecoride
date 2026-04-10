document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('chatBox');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');

    const trajetId = chatBox.dataset.trajet;
    const currentUserId = chatBox.dataset.user;
    const chauffeurId = document.getElementById('chauffeur_id').value;
    // Garde l'Id du message précédent pour ne pas afficher en double
    let lastMessageId = parseInt(chatBox.dataset.lastid) || 0;

    function checkNewMessages() {
        fetch(`check_messages.php?trajet_id=${trajetId}&last_id=${lastMessageId}`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    data.forEach(msg => {
                        if (parseInt(msg.message_id) > lastMessageId) {
                            appendMessage(msg);
                            lastMessageId = parseInt(msg.message_id);
                        }
                    });
                    scrollToBottom();
                }
            })
            .catch(err => console.error("Erreur check:", err));
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault(); // Empeche le rechargement de la page pour faire comme une messagerie
        const content = messageInput.value.trim();
        if (!content) return;

        const formData = new FormData();
        formData.append('trajet_id', trajetId);
        formData.append('chauffeur_id', chauffeurId);
        formData.append('message', content);

        fetch('envoyer_message.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                messageInput.value = "";
                checkNewMessages();
            }
        })
        .catch(err => console.error("Erreur envoi:", err));
    });

    function appendMessage(msg) {
        // Verification a qui appartien le message pour agir sur le html css
        const isMe = (msg.expediteur_id == currentUserId);
        const div = document.createElement('div');
        div.className = `msg ${isMe ? 'msg-me' : 'msg-them'}`;

        const date = msg.date_envoi ? new Date(msg.date_envoi) : new Date();
        const time = date.getHours().toString().padStart(2, '0') + ":" + 
                     date.getMinutes().toString().padStart(2, '0');

        div.innerHTML = `${msg.contenu} <span class="msg-date">${time}</span>`;
        chatBox.appendChild(div);
    }

    function scrollToBottom() {
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    scrollToBottom();
    // Appel check_message.php toute les 3 secondes pour faire comme si la messagerie est en temps reel
    setInterval(checkNewMessages, 3000);
});