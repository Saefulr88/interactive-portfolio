-- SQLite schema: products, transactions, transaction_items, users, returns, return_items
PRAGMA foreign_keys = ON;

CREATE TABLE IF NOT EXISTS products (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  sku TEXT,
  name TEXT NOT NULL,
  price INTEGER NOT NULL,
  stock INTEGER DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transactions (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  invoice TEXT UNIQUE,
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  total INTEGER NOT NULL,
  payment INTEGER NOT NULL,
  change INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS transaction_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  transaction_id INTEGER NOT NULL,
  product_id INTEGER,
  name TEXT NOT NULL,
  price INTEGER NOT NULL,
  quantity INTEGER NOT NULL,
  subtotal INTEGER NOT NULL,
  FOREIGN KEY (transaction_id) REFERENCES transactions(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id)
);

CREATE TABLE IF NOT EXISTS users (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  username TEXT UNIQUE NOT NULL,
  password_hash TEXT NOT NULL,
  name TEXT,
  role TEXT DEFAULT 'cashier',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS returns (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  return_no TEXT UNIQUE,
  date DATETIME DEFAULT CURRENT_TIMESTAMP,
  transaction_id INTEGER,
  total INTEGER,
  created_by INTEGER,
  FOREIGN KEY(transaction_id) REFERENCES transactions(id),
  FOREIGN KEY(created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS return_items (
  id INTEGER PRIMARY KEY AUTOINCREMENT,
  return_id INTEGER NOT NULL,
  transaction_item_id INTEGER,
  product_id INTEGER,
  name TEXT,
  price INTEGER,
  quantity INTEGER,
  subtotal INTEGER,
  FOREIGN KEY(return_id) REFERENCES returns(id) ON DELETE CASCADE,
  FOREIGN KEY(product_id) REFERENCES products(id)
);
