-- Quick SQL seeder for demonstration
INSERT INTO categories (name, slug, icon, color, created_at, updated_at) VALUES
('Restaurantes', 'restaurantes', 'utensils', '#ef4444', NOW(), NOW()),
('Tecnología', 'tecnologia', 'laptop', '#3b82f6', NOW(), NOW()),
('Salud', 'salud', 'heart', '#10b981', NOW(), NOW());

INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES
('Super Admin', 'admin@conectahn.com', '$2y$12$KxOA', 'super_admin', NOW(), NOW()),
('Seller', 'seller@test.com', '$2y$12$KxOA', 'seller', NOW(), NOW());

INSERT INTO tenants (user_id, name, slug, category_id, description, city, status, is_pro, logo_path, primary_color, whatsapp_number, created_at, updated_at) VALUES
(2, 'Café El Indio', 'cafe-el-indio', 1, 'El mejor café hondureño', 'Tegucigalpa', 'approved', 1, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=400', '#7c3aed', '+504 9999-9999', NOW(), NOW()),
(2, 'TechStore SPS', 'techstore-sps', 2, 'Tecnología de calidad', 'San Pedro Sula', 'approved', 1, 'https://images.unsplash.com/photo-1531297484001-80022131f5a1?w=400', '#dc2626', '+504 8888-8888', NOW(), NOW()),
(2, 'Farmacia Salud Total', 'farmacia-salud-total', 3, 'Tu salud es nuestra prioridad', 'La Ceiba', 'approved', 1, 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400', '#059669', '+504 7777-7777', NOW(), NOW()),
(2, 'Restaurante Don Pepe', 'restaurante-don-pepe', 1, 'Comida típica hondureña', 'Tegucigalpa', 'approved', 1, 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400', '#1e40af', '+504 6666-6666', NOW(), NOW()),
(2, 'Inversiones La Paz', 'inversiones-la-paz', 2, 'Tecnología empresarial', 'San Pedro Sula', 'approved', 1, 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=400', '#ea580c', '+504 5555-5555', NOW(), NOW()),
(2, 'Comedor Suyapa', 'comedor-suyapa', 1, 'Comida casera', 'Tegucigalpa', 'approved', 0, 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400', '#6b7280', '+504 4444-4444', NOW(), NOW()),
(2, 'Tienda La Esquina', 'tienda-la-esquina', 2, 'Todo lo que necesitas', 'La Ceiba', 'approved', 0, 'https://images.unsplash.com/photo-15041800000000?w=400', '#6b7280', '+504 3333-3333', NOW(), NOW()),
(2, 'Barbería El Corte Perfecto', 'barberia-el-corte-perfecto', 3, 'Estilo y calidad', 'San Pedro Sula', 'approved', 0, 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?w=400', '#6b7280', '+504 2222-2222', NOW(), NOW());
