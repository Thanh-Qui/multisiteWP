import React from 'react';
import { Modal, Image,Button } from "antd";

const ProductQuickView = ({ product }) => {
    const [isModalVisible, setIsModalVisible] = React.useState(false);

    const showModal = () => {
        setIsModalVisible(true);
    };

    const hiddenModal = () => {
        setIsModalVisible(false);
    };

    if (!product) {
        return null;
    }

    return (
        <>
            <button onClick={showModal} className='btn btn-primary'>
                Quick View
            </button>

            <Modal
                open={isModalVisible}
                onCancel={hiddenModal}
                footer={null}
                width={1000}
            >
                <div className="row">
                    <div className="col-md-7">
                        <Image
                            src={product.image_url}
                            alt={product.title}
                            className="img-fluid w-100"
                        />
                    </div>
                    <div className="col-md-4">
                        <h2 className="mb-3">{product.title}</h2>
                        <p className="fw-bold fs-4 text-danger">
                            {product.price}đ
                        </p>
                        <p>{product.content}</p>

                        <Button type="primary" size="large">
                            Add to Cart
                        </Button>
                    </div>
                </div>

            </Modal>
        </>
    );
};

export default ProductQuickView;