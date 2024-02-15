import SetUpInstance from "../utils/axios.js";

const http = SetUpInstance();

export const getUser = async (id) => {
    try {
        const response = await http.get(`/users/${id}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getUserServicePlanning = async (idUser, idService, page) => {
    try {
        const response = await http.get(`/plannings/${idUser}/${idService}?page=${page}`);
        return response.data['hydra:member'];
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getUserReservations = async (idUser, page) => {
    try {
        const response = await http.get(`/users/${idUser}/reservations?page=${page}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const getTroubleMakerReservations = async (idUser, page) => {
    try {
        const response = await http.get(`/users/trouble-maker/${idUser}/reservations?page=${page}`);
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}

export const updateUser = async (id, data) => {
    try {
        const response = await http.patch(`/users/${id}`, data, {
            headers: {
                'Content-Type': 'application/merge-patch+json',
            },
        });
        return response.data;
    } catch (error) {
        window.location.href = '/error';
    }
}