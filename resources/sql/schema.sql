CREATE TABLE transactions (
    id  INTEGER PRIMARY KEY AUTOINCREMENT,
    operationdate    TEXT NOT NULL,
    valuedate    TEXT NOT NULL,
    description    TEXT NOT NULL,
    reason    TEXT NOT NULL,
    revenue    INTEGER NOT NULL,
    expenditure    INTEGER NOT NULL,
    currency    TEXT NOT NULL
);


INSERT INTO transactions (operationdate, valuedate, description, reason, revenue, expenditure, currency)
VALUES
('17/09/2011', '17/09/2011', 'PAGAMENTO TRAMITE POS', 'POS CARTA 124567 DEL 17/09/2011 ORE 13:44 C/O 1234567890 PINCO PALLO', 'NULL', '-18.11', 'EUR');

CREATE TABLE underscore (
    id  INTEGER NOT NULL,
    created    TEXT NOT NULL
);
