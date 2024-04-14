export const validateRequiredFields = (inputData: string | null, fieldName: string): void => {
    if (inputData == null) {
        throw new Error(`${fieldName}: null is not allowed.`);
    }
    if (inputData == undefined) {
        throw new Error(`${fieldName}: undefined is not allowed.`);
    }
    if (inputData == "") {
        throw new Error(`${fieldName}: Empty string is not allowed.`);
    }
};

export const validateEmail = (email: string): void => {
    const regex: RegExp = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!regex.test(email)) {
        throw new Error('Invalid email format.');
    }
}


