document.addEventListener('DOMContentLoaded', () => {
    const idChat = window.CHAT_CONFIG.idChat;
    const currentUserId = window.CHAT_CONFIG.userId;
    const messageContainer = document.querySelector('main section[aria-label="Cronologia messaggi"]');
    if (messageContainer) {
        messageContainer.scrollTop = messageContainer.scrollHeight;
    }
    const chatForm = document.getElementById('chat-form');
    if (chatForm) {
        const fileInput = document.getElementById('image-upload');
        const messageInput = document.getElementById('message-input');
        const sendButton = chatForm.querySelector('[type="submit"]');
        const previewContainer = document.getElementById('image-preview');
        const previewImage = document.getElementById('preview-image');
        const removeBtn = document.getElementById('remove-image-btn');

        function checkFormValidity() {
            const hasText = messageInput.value.trim().length > 0;
            const hasImage = fileInput.files.length > 0;
            if (sendButton) sendButton.disabled = !(hasText || hasImage);
        }

        checkFormValidity();
        messageInput.addEventListener('input', checkFormValidity);

        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewContainer.style.display = 'flex';
                }
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
            checkFormValidity();
            messageInput.focus();
        });

        removeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            fileInput.value = '';
            previewContainer.style.display = 'none';
            previewImage.src = '';
            checkFormValidity();
        });
    }

    let lastReceivedProgressivo = 0;
    const POLLING_INTERVAL = 1000;
    let isPollingActive = true;

    const base64ToBlob = (base64, mimeType = 'image/jpeg') => {
        try {
            const byteCharacters = atob(base64);
            const byteNumbers = new Array(byteCharacters.length);
            for (let i = 0; i < byteCharacters.length; i++) {
                byteNumbers[i] = byteCharacters.charCodeAt(i);
            }
            const byteArray = new Uint8Array(byteNumbers);
            const blob = new Blob([byteArray], { type: mimeType });
            return URL.createObjectURL(blob);
        } catch (e) {
            console.error("Failed to convert base64 to blob", e);
            return null;
        }
    };

    const initializeLastProgressivo = () => {
        const messages = messageContainer.querySelectorAll('article');
        messages.forEach(msg => {
            const prog = parseInt(msg.dataset.progressivo);
            if (!isNaN(prog) && prog > lastReceivedProgressivo) {
                lastReceivedProgressivo = prog;
            }
        });
    };

    const renderMessage = (row, idCurrentUser) => {
        const isMine = (row.idMandante == idCurrentUser);
        const whoSent = !isMine ? "sent" : "received";

        if (row.type === 'offer') {
            let headerText, icon, footerHtml;

            if (isMine) {
                headerText = "La tua offerta";
                icon = '<i class="fas fa-arrow-up text-secondary"></i>';
                footerHtml = `
                    <div class="mt-2 pt-2 border-top border-secondary-subtle text-muted small fst-italic text-center">
                        <i class="fas fa-clock me-1"></i> In attesa di risposta...
                    </div>`;
            } else {
                headerText = "Offerta ricevuta";
                icon = '<i class="fas fa-tag text-success"></i>';

            

                footerHtml = `
        <div class="mt-3">
            <form method="POST" action="utils/acceptOffer.php">
                <input type="hidden" name="chatId" value="${idChat}">
                <button type="submit" class="btn btn-success btn-sm w-100 fw-bold shadow-sm" style="border-radius: 20px;">
                    Accetta Offerta
                </button>
            </form>
        </div>`;
            }

            return `
            <article data-type="${whoSent}" data-progressivo="${row.progressivo}">
                <div style="min-width: 220px;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <small class="fw-bold text-uppercase text-secondary" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            ${headerText}
                        </small>
                        ${icon}
                    </div>
                    <div class="text-center py-1">
                        <span class="display-6 fw-bold text-dark">€ ${row.content}</span>
                    </div>
                    ${footerHtml}
                </div>
            </article>`;
        }

        let content = row.content || '';
        let base64String = row.immage_blob || null;
        let imageHtml = '';

        if (base64String) {
            const blobUrl = base64ToBlob(base64String, 'image/jpeg');
            if (blobUrl) {
                imageHtml = `<img src="${blobUrl}" alt="Immagine" style="max-width: 100%; height: auto; border-radius: 8px; margin-bottom: 5px;">`;
            }
        }

        const textHtml = content ? `<p>${content}</p>` : '';

        return `
            <article data-type="${whoSent}" data-progressivo="${row.progressivo}">
                <div>
                    ${imageHtml}
                    ${textHtml}
                </div>
            </article>
        `;
    };

    const pollForNewMessages = async () => {
        if (!messageContainer || !idChat || !isPollingActive) {
            return;
        }
        try {
            const response = await fetch(`utils/getMessages.php?last_prog=${lastReceivedProgressivo}`);

            if (!response.ok) {
                const errorHtml = `      
                    <div class="alert alert-warning text-center p-5" style="background-color: #fff3cd; color:rgb(0, 0, 0); border: 1px solid #ffeeba; border-radius: 5px; margin-top: 20px;">
                        <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Chat non disponibile</h2>
                        <p style="margin-bottom: 1.5rem;">Al momento il servizio è temporaneamente non disponibile. Ci scusiamo per il disagio.</p>
                    </div>
                `;

                console.log(response)
                messageContainer.insertAdjacentHTML('beforeend', errorHtml);
                messageContainer.scrollTop = messageContainer.scrollHeight;

                isPollingActive = false;
                return;
            }
            const data = await response.json();

            if (data.messages && data.messages.length > 0) {

                let mBlock = document.getElementById("noMessageBlock");
                console.log(mBlock);
                mBlock.remove();

                data.messages.forEach(msg => {
                    const newMessageHtml = renderMessage(msg, currentUserId);
                    messageContainer.insertAdjacentHTML('beforeend', newMessageHtml);

                    if (msg.progressivo > lastReceivedProgressivo) {
                        lastReceivedProgressivo = msg.progressivo;
                    }
                });
                messageContainer.scrollTop = messageContainer.scrollHeight;
            }
        } catch (error) {
            console.error('Polling failed:', error);
        }

        if (isPollingActive) {
            setTimeout(pollForNewMessages, POLLING_INTERVAL);
        }
    };

    if (messageContainer) {
        initializeLastProgressivo();
        if (idChat !== null) {
            pollForNewMessages();
        }
    }
});