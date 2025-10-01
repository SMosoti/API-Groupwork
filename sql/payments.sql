CREATE TABLE payments (
    id SERIAL PRIMARY KEY,
    reservation_id INT REFERENCES reservations(id) ON DELETE CASCADE,
    amount NUMERIC(10,2) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    method VARCHAR(20) CHECK (method IN ('cash', 'card', 'mpesa', 'paypal')),
    status VARCHAR(20) CHECK (status IN ('pending', 'paid', 'failed')) DEFAULT 'pending'
);
