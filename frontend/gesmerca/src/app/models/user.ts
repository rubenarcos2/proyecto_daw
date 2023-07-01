import { Role } from './role';
import { Permissions } from './permissions';

export interface User {
    id?: number;
    name?: string;
    email?: string;
    created_at?: Date;
    updated_at?: Date;
    email_verified_at?: Date;
    roles?: Role[];
    permissions?: Permissions[];
}