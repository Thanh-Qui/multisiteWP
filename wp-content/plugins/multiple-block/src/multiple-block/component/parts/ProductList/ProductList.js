import { getAllProductList } from "../../../../../common/js/api/product-list";
import { useEffect, useState } from "react";
import ProductQuickView from "../QuickView/ProductQuickView";

export default function ProductList({ selectedCategory }) {
    const [products, setProducts] = useState([]);

    useEffect(() => {
        async function fetchProducts() {
            try {
                const data = await getAllProductList();
                setProducts(data);
            } catch (error) {
                console.error('Error fetching products:', error);
            }
        }

        fetchProducts();
    }, [])

    const filteredProducts = selectedCategory
        ? products.filter(product => product.category_id == selectedCategory)
        : products;

    return (
        <>
            <div className="row g-3">
                {filteredProducts?.map((product) => (
                    <div
                        className="col-12 col-sm-6 col-md-4 col-lg-3"
                        key={product.id}
                    >
                        <div className="card h-100">
                            <div className="card-body">
                                <img src={product.image_url} alt={product.title} className="card-img-top mb-3" />
                                <h5>{product.title}</h5>
                                <p>{product.price}đ</p>
                                <p>{product.content.length > 100 ? product.content.slice(0, 100) + '...' : product.content}</p>
                                <ProductQuickView product={product} />
                            </div>
                        </div>

                    </div>
                ))}
            </div>
        </>
    )
}