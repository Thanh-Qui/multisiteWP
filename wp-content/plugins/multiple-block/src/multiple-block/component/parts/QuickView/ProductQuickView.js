import React from 'react';
import { Modal, Image, Button } from "antd";
import { message } from "antd";

export default function ProductQuickView ({ product }) {
    const [isModalVisible, setIsModalVisible] = React.useState(false);
    const [loadingAddTocart, setLoadingAddTocart] = React.useState(false);

    const showModal = () => {
        setIsModalVisible(true);
    };

    const hiddenModal = () => {
        setIsModalVisible(false);
    };

    if (!product) {
        return null;
    }

    const handleAddToCart = () => {
        setLoadingAddTocart(true);

        const config = window.addToCart || {};
        const nonce = config.nonce ? config.nonce : config.security;
        const ajaxUrl = config.ajaxUrl || {};

        console.log(config);
        console.log('Using AJAX URL:', ajaxUrl);
        console.log('Using Nonce:', nonce);

        const formData = new FormData();
        formData.append('action', 'test_nonce_process');
        formData.append('security', nonce);
        formData.append('product_id', product.id);
        
        fetch(ajaxUrl, {
            method: 'POST',
            body: formData,
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text();
        })
        .then(text => {
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    message.success(`Thành công: ${data.data}`);
                } else {
                    message.error(`Lỗi: ${data.data}`);
                }
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                console.error('Response text:', text);
                message.error("Response từ server không hợp lệ.");
            }
        })
        .catch(error => {
            console.error('Error:', error);
            message.error(`Có lỗi xảy ra khi gửi request: ${error.message}`);
        })
        .finally(() => {
            setLoadingAddTocart(false);
        });
    };

    return (
        <>
            <button onClick={showModal} className='btn btn-primary'>
                Quick View
            </button>

            <Modal open={isModalVisible} onCancel={hiddenModal} footer={null} width={1000}>
                <div className="row">
                    <div className="col-md-7">
                        <Image src={product.image_url} alt={product.title} className="img-fluid w-100"
                        />
                    </div>
                    <div className="col-md-4">
                        <h2 className="mb-3">{product.title}</h2>
                        <p className="fw-bold fs-4 text-danger">
                            {product.price}đ
                        </p>
                        <p>{product.content}</p>

                        <Button type="primary" size="large" loading={loadingAddTocart} onClick={handleAddToCart}>
                            Add to Cart
                        </Button>
                    </div>
                </div>

            </Modal>
        </>
    );
};
