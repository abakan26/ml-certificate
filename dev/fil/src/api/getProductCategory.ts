import {API_URL} from './constans';

export default async function getProductCategory() {
    const response = await fetch(API_URL + '?action=ml_get_product_category');
    const productCategories = await response.json();
    return productCategories.map(cat => ({
        value: cat.id.toString(),
        label: cat.name
    }));
}
