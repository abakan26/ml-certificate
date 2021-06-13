import {API_URL} from './constans';

export default async function getCourses(categoryId: string) {
    const body = new FormData();
    body.append('categoryId', categoryId);
    const response = await fetch(API_URL + '?action=ml_get_products_by_category_array',
        {
            method: 'POST',
            body
        }
    );
    const courses = await response.json();
    return courses.map(
        data => ({
            id: data.product_id.toString(),
            title: data.product_name,
        })
    );
}
