export interface Product {
    id?: number;
    name?: string;
    description?: Text;
    price?: number;
    image?: string;    
    thumbail_32x32?: string;
    thumbail_128x128?: string;
    stock?: number;
    isDeleting?: boolean;
}