CREATE TABLE transactions (
    id  INTEGER PRIMARY KEY AUTOINCREMENT,
    operationDate    TEXT NOT NULL,
    valueDate    TEXT NOT NULL,
    description    TEXT NOT NULL,
    reason    TEXT NOT NULL,
    revenue    INTEGER NOT NULL,
    expenditure    INTEGER NOT NULL,
    currency    TEXT NOT NULL,
);
