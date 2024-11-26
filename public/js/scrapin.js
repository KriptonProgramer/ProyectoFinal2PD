const axios = require('axios');
const cheerio = require('cheerio');
const fs = require('fs');

async function obtenerDatos(url) {
    try {
        // Realiza la solicitud HTTP
        const { data } = await axios.get(url);

        // Carga el HTML en cheerio
        const $ = cheerio.load(data);

        // Selecciona y extrae datos. Ajusta los selectores según sea necesario.
        const productos = [];

        // Supongamos que los productos están en un elemento con la clase '.producto'
        $('.producto').each((index, element) => {
            const nombre = $(element).find('.sc-kSsbVf eKTcFN').text(); // Cambia el selector según el HTML
            const precio = $(element).find('.sc-dxlmjS dtCHz').text(); // Cambia el selector según el HTML
            const imagen = $(element).find('.sc-dZxRDy cmktJa').attr('src'); // Cambia el selector según el HTML

            // Agrega el producto a la lista
            productos.push({ nombre, precio, imagen });
        });
        fs.writeFileSync('productos.json', JSON.stringify(productos, null, 2));

        console.log(productos); // Muestra los productos en la consola
    } catch (error) {
        console.error('Error al obtener datos:', error);
    }
}

// Ejemplo de uso
const url = 'https://www.fravega.com/';
obtenerDatos(url);
