import TabCategory from '../parts/CategoryTabs/TabCategory';
import 'bootstrap/dist/css/bootstrap.min.css';
import { useState } from 'react';

const ProductListContainer = () => {
    const [selectedCategory, setSelectedCategory] = useState(null);

    return (
        <div className="container">
            <TabCategory onSelectCategory={setSelectedCategory} />
        </div>

    );
};

export default ProductListContainer;