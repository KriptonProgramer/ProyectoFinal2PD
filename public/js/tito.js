const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

async function obtenerDatos(url) {
    try {
        const { data } = await axios.get(url);
        const $ = cheerio.load(data);
        const productos = [];

        // Selector del artículo
        $('article[data-test-id="product-layout"]').each((index, element) => {
            const nombre = $(element).find('[data-test-id="product-title"]').text().trim();
            const precioActual = $(element).find('[data-test-id="product-price"] .sc-feNupb').text().trim();
            const precioAnterior = $(element).find('[data-test-id="product-price"] .sc-bHvAfQ').text().trim();
            const imagen = $(element).find('img').first().attr('src');

            // Formatear precios
            const precioNumericoActual = parseFloat(precioActual.replace(/[^0-9.-]+/g, ""));
            const precioNumericoAnterior = parseFloat(precioAnterior.replace(/[^0-9.-]+/g, ""));

            productos.push({
                nombre,
                precioActual: precioNumericoActual,
                precioAnterior: precioNumericoAnterior,
                imagen
            });
        });

        // Guarda los datos en un archivo JSON
        fs.writeFileSync('productos.json', JSON.stringify(productos, null, 2));
        console.log('Datos guardados en productos.json');

    } catch (error) {
        console.error('Error al obtener datos:', error);
    }
}

// URL de la página de productos
const url = 'https://www.fravega.com/';
obtenerDatos(url);
