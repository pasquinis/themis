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
('2011-09-17', '2011-09-17', 'PAGAMENTO TRAMITE POS', 'POS CARTA 124567 DEL 17/09/2011 ORE 13:44 C/O 1234567890 PINCO PALLO', 'NULL', '-18.11', 'EUR');

CREATE TABLE underscore (
    id  INTEGER NOT NULL,
    created    TEXT NOT NULL
);

CREATE TABLE project_household_budget (
    operationdate    TEXT NOT NULL,
    category   TEXT NOT NULL,
    revenue    INTEGER NOT NULL,
    expenditure    INTEGER NOT NULL,
    transaction TEXT NOT NULL,
    projected_at TEXT NOT NULL
);

/* TODO fix the wrong fixtures
INSERT INTO project_household_budget (operationdate, category, revenue, expenditure)
VALUES
('2011-09-17', 'cat A', 0, 12),
('2011-09-18', 'cat A', 0, 11),
('2011-09-18', 'cat B', 20, 0);
*/
