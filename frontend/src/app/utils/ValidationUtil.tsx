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

export const validateUsername = (username: string): void => {
    const invalidPattern: RegExp = /[^a-zA-Z0-9]/;
    if (invalidPattern.test(username)) {
        throw new Error('Username can contain only single-byte alphanumeric characters and numbers.');
    }

    const minCharCount = 1;
    if (username.length < minCharCount) {
        throw new Error(`Username must be at least ${minCharCount} characters.`);
    }

    const maxCharCount = 15;
    if (username.length > maxCharCount) {
        throw new Error(`Username must be up to ${maxCharCount} characters.`);
    }
}

export const validatePassword = (password: string): void => {
    const invalidPattern: RegExp = /[^a-zA-Z0-9!-\/:-@\[-`\{-~]/;
    if (invalidPattern.test(password)) {
        throw new Error('Password can contain only single-byte alphanumeric characters, numbers and symbols.');
    }

    let typesIncluded: number = 0;
    if (/[a-z]/.test(password)) {
        typesIncluded++;
    }
    if (/[A-Z]/.test(password)) {
        typesIncluded++;
    }
    if (/[0-9]/.test(password)) {
        typesIncluded++;
    }
    if (/[!-\/:-@\[-`\{-~]/.test(password)) {
        typesIncluded++;
    }

    const minTypesCount = 4;
    if (typesIncluded < minTypesCount) {
        throw new Error('Password must include all four types of uppercase and lowercase letters, numbers and symbols.');
    }

    const minPasswordChars = 8;
    if (password.length < minPasswordChars) {
        throw new Error('Password must be at least 8 characters.');
    }
};

export const validateCharCount = (
    message: string, itemName: string, minCharCount: number | null, maxCharCount: number | null
): void => {
    if (minCharCount !== null && message.length < minCharCount) {
        throw new Error(`${itemName} must be at least ${minCharCount} characters.`);
    }
    if (maxCharCount !== null && message.length > maxCharCount) {
        throw new Error(`${itemName} must be up to ${maxCharCount} characters.`);
    }
};
