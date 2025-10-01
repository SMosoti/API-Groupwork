CREATE TABLE rooms (
    id SERIAL PRIMARY KEY,
    room_number VARCHAR(10) UNIQUE NOT NULL,
    type VARCHAR(50) NOT NULL,  -- e.g., Single, Double, Suite
    price_per_night NUMERIC(10,2) NOT NULL,
    status VARCHAR(20) CHECK (status IN ('available', 'booked', 'maintenance')) DEFAULT 'available'
);
