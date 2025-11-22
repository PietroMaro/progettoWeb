<?php
?>

<style>
/* --- GLOBAL RESET (Only for this page) --- */
/* We still need to stop the main window scroll to make it feel like an app */
body {
    margin: 0;
    padding: 0;
    overflow: hidden; 
    background-color: #d1d7db;
}

/* --- MAIN CONTAINER SCOPING --- */
#chat-app {
    display: flex;
    /* Adjust '60px' if your Navbar height is different */
    height: calc(100vh - 60px); 
    width: 100vw;
    background-color: white;
    font-family: "Segoe UI", Helvetica, Arial, sans-serif;
}

/* --- ASIDE (Contact List) --- */
#chat-app aside {
    width: 30%;
    min-width: 300px;
    border-right: 1px solid #e9edef;
    background-color: white;
    display: flex;
    flex-direction: column;
}

#chat-app aside ul {
    list-style: none;
    padding: 0;
    margin: 0;
    overflow-y: auto;
    flex-grow: 1;
}

#chat-app aside li {
    border-bottom: 1px solid #f0f2f5;
    cursor: pointer;
    transition: background 0.2s;
}

#chat-app aside li:hover {
    background-color: #f5f6f6;
}

/* Attribute selector inside the ID */
#chat-app aside li[aria-current="true"] {
    background-color: #f0f2f5;
}

#chat-app aside button {
    all: unset;
    width: 100%;
    padding: 12px 15px;
    display: flex;
    align-items: center;
    box-sizing: border-box;
}

#chat-app aside .text-info {
    flex-grow: 1;
    margin-left: 15px;
    overflow: hidden;
}

#chat-app aside .name-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 3px;
}

#chat-app aside strong {
    color: #111b21;
    font-weight: 500;
}

#chat-app aside time {
    font-size: 12px;
    color: #667781;
}

#chat-app aside small {
    color: #667781;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* --- MAIN CHAT AREA --- */
#chat-app main {
    width: 70%;
    display: flex;
    flex-direction: column;
    background-color: #efeae2;
    position: relative;
}

#chat-app main::before {
    content: "";
    position: absolute;
    top: 0; left: 0; width: 100%; height: 100%;
    background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png');
    opacity: 0.4;
    z-index: 0;
    pointer-events: none;
}

/* --- HEADER (Scoped to chat-app) --- */
#chat-app header {
    padding: 10px 16px;
    background-color: #f0f2f5;
    border-bottom: 1px solid #e9edef;
    display: flex;
    justify-content: space-between;
    align-items: center;
    z-index: 1;
}

#chat-app header .user-info {
    display: flex;
    align-items: center;
}

#chat-app header h2 {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    color: #111b21;
}

#chat-app header nav button {
    background: none;
    border: none;
    color: #54656f;
    font-size: 20px;
    margin-left: 20px;
    cursor: pointer;
}

/* --- MESSAGES SECTION --- */
#chat-app section[aria-label="Cronologia messaggi"] {
    flex-grow: 1;
    overflow-y: auto;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 2px;
    z-index: 1;
}

#chat-app article {
    display: flex;
    margin-bottom: 8px;
}

#chat-app article > div {
    padding: 6px 7px 8px 9px;
    border-radius: 7.5px;
    box-shadow: 0 1px 0.5px rgba(0,0,0,0.13);
    max-width: 65%;
    position: relative;
    display: flex;
    flex-direction: column;
}

#chat-app article p {
    margin: 0 0 4px 0;
    font-size: 14.2px;
    line-height: 19px;
    color: #111b21;
}

#chat-app article time {
    font-size: 11px;
    color: #667781;
    align-self: flex-end;
}

/* Received vs Sent */
#chat-app article[data-type="received"] {
    justify-content: flex-start;
}
#chat-app article[data-type="received"] > div {
    background-color: white;
    border-top-left-radius: 0;
}

#chat-app article[data-type="sent"] {
    justify-content: flex-end;
}
#chat-app article[data-type="sent"] > div {
    background-color: #d9fdd3;
    border-top-right-radius: 0;
}

#chat-app .msg-meta {
    display: flex;
    align-self: flex-end;
    align-items: center;
    gap: 4px;
}

#chat-app .msg-meta i {
    font-size: 10px;
    color: #53bdeb;
}

/* --- FOOTER (Scoped to chat-app) --- */
#chat-app footer {
    background-color: #f0f2f5;
    padding: 10px 16px;
    z-index: 1;
}

#chat-app footer form {
    display: flex;
    align-items: center;
    gap: 10px;
}

#chat-app footer button {
    background: none;
    border: none;
    font-size: 24px;
    color: #54656f;
    cursor: pointer;
    padding: 5px;
}

/* Hidden Labels */
#chat-app footer label {
    position: absolute;
    width: 1px; height: 1px; margin: -1px;
    overflow: hidden; clip: rect(0,0,0,0);
}

#chat-app footer input[type="text"] {
    flex-grow: 1;
    border: none;
    border-radius: 8px;
    padding: 9px 12px;
    font-size: 15px;
    outline: none;
}

#chat-app footer input[type="text"]:focus {
    background-color: white;
}

/* --- DOUBLE AVATARS (Scoped) --- */
#chat-app aside div[aria-hidden="true"], 
#chat-app header div[aria-hidden="true"] {
    position: relative;
    width: 50px;
    height: 50px;
    margin-right: 15px;
    flex-shrink: 0;
}

#chat-app div[aria-hidden="true"] img {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    border: 2px solid white;
    position: absolute;
    object-fit: cover;
}

#chat-app div[aria-hidden="true"] img:first-of-type {
    top: 0;
    right: 0;
    z-index: 1;
}

#chat-app div[aria-hidden="true"] img:last-of-type {
    bottom: 0;
    left: 0;
    z-index: 2;
}
</style>

<?php
// Assicurati di linkare il file CSS nell'head del tuo template principale
// <link rel="stylesheet" href="style.css">
?>

<div id="chat-app">
    
    <aside aria-label="Lista contatti">
        <ul>
            <li aria-current="true">
                <button aria-label="Chat con Mario Rossi">
                    <div aria-hidden="true">
                        <img src="https://placehold.co/35/blue/white?text=Item" alt="">
                        <img src="https://placehold.co/35/red/white?text=User" alt="">
                    </div>
                    <div class="text-info">
                        <div class="name-row">
                            <strong>Mario Rossi</strong>
                            <time>12:30</time>
                        </div>
                        <small>Hai visto i nuovi prodotti?</small>
                    </div>
                </button>
            </li>

            <li>
                <button aria-label="Chat con Luigi Verdi">
                    <div aria-hidden="true">
                        <img src="https://placehold.co/35/yellow/black?text=Book" alt="">
                        <img src="https://placehold.co/35/green/white?text=Luigi" alt="">
                    </div>
                    <div class="text-info">
                        <div class="name-row">
                            <strong>Luigi Verdi</strong>
                            <time>Ieri</time>
                        </div>
                        <small>Ok, ci sentiamo dopo.</small>
                    </div>
                </button>
            </li>
        </ul>
    </aside>

    <main role="main" aria-label="Conversazione corrente">
        
        <header>
            <div class="user-info">
                <div aria-hidden="true">
                    <img src="https://placehold.co/35/blue/white?text=Item" alt="">
                    <img src="https://placehold.co/35/red/white?text=User" alt="">
                </div>
                <h2>Mario Rossi</h2>
            </div>
            
            <nav>
                <button type="button" aria-label="Cerca">
                    <i class="fas fa-search"></i>
                </button>
                <button type="button" aria-label="Allega">
                    <i class="fas fa-paperclip"></i>
                </button>
            </nav>
        </header>

        <section aria-live="polite" aria-label="Cronologia messaggi">
            
            <article data-type="received">
                <div>
                    <p>Ciao! Come va?</p>
                    <time datetime="10:30">10:30</time>
                </div>
            </article>

            <article data-type="sent">
                <div>
                    <p>Tutto bene! Ho appena finito il login.</p>
                    <div class="msg-meta">
                        <time datetime="10:32">10:32</time>
                        <i class="fas fa-check-double" aria-label="Letto"></i>
                    </div>
                </div>
            </article>

             <article data-type="received">
                <div>
                    <p>Ottimo lavoro!</p>
                    <time datetime="10:33">10:33</time>
                </div>
            </article>

        </section>

        <footer>
            <form action="" method="POST">
                <button type="button" aria-label="Emoji">
                    <i class="far fa-smile"></i>
                </button>
                
                <button type="button" aria-label="Allega">
                    <i class="fas fa-plus"></i>
                </button>
                
                <label for="message-input">Scrivi un messaggio</label>
                <input type="text" id="message-input" name="message" placeholder="Scrivi un messaggio" autocomplete="off">
                
                <button type="submit" aria-label="Invia">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </footer>

    </main>
</div>