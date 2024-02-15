import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getCategory = async (id) => {
    try {
        const response = await http.get(`/categories/${id}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getCategories = async () => {
    try {
        const response = await http.get(`/categories`);
        return response.data['hydra:member'];
    } catch (error) {
        return null;
    }
}

export const createCategory = async (name) => {
    try {
        await http.post(`/categories`, { name });
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateCategory = async (id, name) => {
    try {
        await http.patch(`/categories/${id}`, { name }, {
            headers: {
                'Content-Type': 'application/merge-patch+json'
            }
        });
    } catch (error) {
        window.location.href = '/error';
    }
}