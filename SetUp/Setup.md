Per poter utilizzare il sito é fondamentale seguire i seguenti passaggi per creare e sistemare il db:
- Creare un db chiamato unisell
- Utilizzare createTables.sql per creare tutte le tabelle del db
- In questa maniera verrá anche creato il primo admin (email: admin@admin, password: admin) 
- Nel caso si desideri inserire altri admin basterá semplicemente modificare e usare la query qui sotto 


INSERT INTO `admin` (`nome`, `cognome`, `email`, `PASSWORD`) VALUES
( 'admin', 'admin', 'admin@admin', 'admin');
