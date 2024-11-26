const { Client } = require('pg');
const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

async function insertarProductos(productos) {
    const client = new Client({
        user: 'postgres',
        host: '10.38.83.3',
        database: 'tienda_php',
        password: 'lancelot',
        port: 5432,
    });

    await client.connect();

    for (const producto of productos) {
        const query = `
            INSERT INTO productos (
                nombre, 
                categoria_id, 
                subcategoria_id, 
                marca, 
                precio, 
                stock, 
                producto_imagen1, 
                producto_imagen2, 
                producto_imagen3, 
                condicion
            ) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)
        `;
        const values = [
            producto.nombre,
            13,  // categoria_id
            3,   // subcategoria_id
            producto.marca,  // Marca
            producto.precioActual,
            10,  // stock
            producto.imagen, // producto_imagen1
            producto.imagen, // producto_imagen2
            producto.imagen, // producto_imagen3
            1    // condicion
        ];
        try {
            await client.query(query, values);
            console.log(`Producto insertado: ${producto.nombre}`);
        } catch (error) {
            console.error('Error al insertar producto:', error);
        }
    }

    await client.end();
}

async function obtenerDatos(url) {
    try {
        const { data } = await axios.get(url);
        const $ = cheerio.load(data);
        const productos = [];

        $('article[data-test-id="product-layout"]').each((index, element) => {
            const nombre = $(element).find('[data-test-id="product-title"]').text().trim();
            const precioActual = $(element).find('[data-test-id="product-price"] .sc-feNupb').text().trim();
            const precioAnterior = $(element).find('[data-test-id="product-price"] .sc-bHvAfQ').text().trim();
            const imagen = $(element).find('img').first().attr('src');

            const precioNumericoActual = parseFloat(precioActual.replace(/[^0-9.-]+/g, ""));
            const precioNumericoAnterior = parseFloat(precioAnterior.replace(/[^0-9.-]+/g, ""));

            productos.push({
                nombre,
                precioActual: precioNumericoActual,
                precioAnterior: precioNumericoAnterior,
                imagen
            });
        });

        // Inserta los productos en la base de datos
        await insertarProductos(productos);
    } catch (error) {
        console.error('Error al obtener datos:', error);
    }
}

// URL de la página de productos
const url = 'https://www.fravega.com/';
obtenerDatos(url);
