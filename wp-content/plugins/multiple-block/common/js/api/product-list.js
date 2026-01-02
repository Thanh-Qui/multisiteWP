import React, { useEffect } from 'react';

export async function getAllProductList() {
    try {
        const response = await fetch('http://multisitewp.test:8080/wp-json/product/v1/get-all-products');
        const data = await response.json();

        console.log('get to:', data.to);

        return data.data;
    } catch (error) {
        console.error('Error fetching products:', error);
        throw error;

    }

}

export async function getCategories() {
    try {
        const respinse = await fetch('http://multisitewp.test:8080/wp-json/product/v1/get-all-categories');
        const data = await respinse.json();

        console.log('get to:', data.to);

        return data.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
        throw error;
    }
}