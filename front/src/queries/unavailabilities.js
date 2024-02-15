import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getUnavailabilities = async () => {
    try {
        const response = await http.get(`/unavailabilities`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getUnavailability = async (id) => {
    try {
        const response = await http.get(`/unavailabilities/${id}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const createUnavailability = async (unavailability) => {
    try {
        const response = await http.post('/unavailabilities', unavailability);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateUnavailability = async (id, unavailability) => {
    try {
        const response = await http.patch(`/unavailabilities/${id}`, unavailability);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const deleteUnavailability = async (id) => {
    try {
        await http.delete(`/unavailabilities/${id}`);
    } catch (error) {
        window.location.href = '/error';
    }
}
