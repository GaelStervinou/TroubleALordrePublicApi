export const HTTP_UNAUTHORIZED_CODE = 401;
export class HttpErrorsFactory {
    constructor(statusCode) {
        switch (statusCode) {
            case HTTP_UNAUTHORIZED_CODE:
                return new UnauthorizedError();
            default:
                break;
        }
    }
}

class HttpErrors extends Error {
    constructor(message, statusCode) {
        super(message);
        this.statusCode = statusCode;
    }

}
class UnauthorizedError extends HttpErrors {
    constructor() {
        super('Vos identifiants sont incorrects.', HTTP_UNAUTHORIZED_CODE);
    }

    getErrorMessage = () => {
        return this.message;
    }
}