USE mi_amazon;

-- Insertar categorías principales
INSERT INTO categorias (nombre) VALUES 
('Suplementos'),
('Proteínas'),
('Creatina'),
('Pre-entrenos'),
('Aminoácidos'),
('Accesorios'),
('Equipamiento'),
('SARMS'),
('Boosters'),
('Shakers');

-- Insertar relaciones producto-categoria basándose en los nombres de productos
-- Productos SARMS (1-10)
INSERT INTO producto_categoria (id_producto, id_categoria) VALUES 
(1, 8), (1, 9),  -- Testosterone Booster
(2, 8),          -- MK-677
(3, 8), (3, 9),  -- Testo-Max
(4, 8),          -- MK-2866
(5, 8), (5, 9),  -- Testo Surge
(6, 8),          -- MK-677 Pro
(7, 8), (7, 9),  -- Testo Pump
(8, 8),          -- MK-2866 Xtreme
(9, 8), (9, 9),  -- Testo Blast
(10, 8);         -- MK-677 Ultra

-- Productos Creatina
INSERT INTO producto_categoria (id_producto, id_categoria) 
SELECT id, 3 FROM productos WHERE nombre LIKE '%Creatina%';

-- Productos Proteína
INSERT INTO producto_categoria (id_producto, id_categoria) 
SELECT id, 2 FROM productos WHERE nombre LIKE '%Protein%' OR nombre LIKE '%Whey%' OR nombre LIKE '%Casein%' OR nombre LIKE '%Isolate%';

-- Productos Pre-entreno
INSERT INTO producto_categoria (id_producto, id_categoria) 
SELECT id, 4 FROM productos WHERE nombre LIKE '%Pre-Entreno%' OR nombre LIKE '%Energy Blast%' OR nombre LIKE '%Nitro Pump%';

-- Productos Aminoácidos
INSERT INTO producto_categoria (id_producto, id_categoria) 
SELECT id, 5 FROM productos WHERE nombre LIKE '%BCAA%' OR nombre LIKE '%EAA%' OR nombre LIKE '%Glutamina%';

-- Productos Shakers
INSERT INTO producto_categoria (id_producto, id_categoria) 
SELECT id, 10 FROM productos WHERE nombre LIKE '%Shaker%';

-- Productos Equipamiento/Accesorios
INSERT INTO producto_categoria (id_producto, id_categoria) 
SELECT id, 7 FROM productos WHERE nombre LIKE '%Cinturón%' OR nombre LIKE '%Gloves%' OR nombre LIKE '%Knee%' OR nombre LIKE '%Muñequeras%';