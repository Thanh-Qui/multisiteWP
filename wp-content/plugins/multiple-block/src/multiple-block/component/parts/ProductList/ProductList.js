import { getAllProductList } from "../../../../../common/js/api/product-list";
import { useEffect, useState } from "react";
import ProductQuickView from "../QuickView/ProductQuickView";

export default function ProductList({ selectedCategory, sortOrder }) {
    const [products, setProducts] = useState([]);
    const { enableQuickView } = window.productListData || {};

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

    const sortedProducts = React.useMemo(() => {
        if (!sortOrder) return filteredProducts;

        return [...filteredProducts].sort((a, b) => {
            const priceA = parseFloat(a.price) || 0;
            const priceB = parseFloat(b.price) || 0;

            if (sortOrder === 'price-asc') {
                return priceA - priceB;
            } else if (sortOrder === 'price-desc') {
                return priceB - priceA;
            }
            return 0;
        });
    }, [filteredProducts, sortOrder]);

    return (
        <>
            <div className="row g-3">
                {sortedProducts?.map((product) => (
                    <div
                        className="col-12 col-sm-6 col-md-4 col-lg-3"
                        key={product.id}
                    >
                        <div className="card h-100">
                            <div className="card-body">
                                <img src={product.image_url ? product.image_url : "http://multisitewp.test:8080/wp-content/uploads/2025/12/default.png"} alt={product.title} className="card-img-top mb-3" />
                                <h5>{product.title}</h5>
                                <p>{product.price}đ</p>
                                <p>{product.content.length > 100 ? product.content.slice(0, 100) + '...' : product.content}</p>

                                {enableQuickView &&
                                    <ProductQuickView product={product} />
                                }


                            </div>
                        </div>

                    </div>
                ))}
            </div>
        </>
    )
}