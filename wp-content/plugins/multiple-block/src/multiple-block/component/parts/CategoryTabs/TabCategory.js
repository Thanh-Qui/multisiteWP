import React, { useEffect, useState } from 'react';
import { getCategories } from '../../../../../common/js/api/product-list';
import 'bootstrap/dist/css/bootstrap.min.css';
import ProductList from '../ProductList/ProductList';
import SortByPrice from './SortByPrice';

export default function TabCategory({ onSelectCategory }) {
    const [categoryList, setCategoryList] = useState([]);
    const [tabSelected, setTabSelected] = useState(null);
    const [sortOrder, setSortOrder] = useState(null);

    useEffect(() => {
        async function fecthCategories() {
            try {
                const data = await getCategories();
                setCategoryList(data);
            } catch (error) {
                console.error('Error fetching categories:', error);
            }
        }
        fecthCategories();
    }, [])

    const handleCategoryClick = (category) => {
        setTabSelected(category.id);
        onSelectCategory(category.id);
    };

    const handleSortChange = (order) => {
        setSortOrder(order);
    };

    return (
        <>
            <ul className="nav nav-tabs mb-3">
                <li className="nav-item">
                    <a className={`nav-link text-slate-950 ${tabSelected === null ? 'active' : ''}`}
                       href="#all"
                       onClick={() => {
                           setTabSelected(null);
                           onSelectCategory(null);
                       }}>
                       All Items
                    </a>
                </li>
                {categoryList?.map((category) => (
                    <li className="nav-item" key={category.id}>
                        <a className={`nav-link text-slate-950 ${tabSelected === category.id ? 'active' : ''}`}
                           href={`#category-${category.id}`}
                           onClick={() => handleCategoryClick(category)}>
                           {category.name}
                        </a>
                    </li>
                ))}
            </ul>
            <SortByPrice onSortChange={handleSortChange} />
            <ProductList selectedCategory={tabSelected} sortOrder={sortOrder} />
        </>
    )
}