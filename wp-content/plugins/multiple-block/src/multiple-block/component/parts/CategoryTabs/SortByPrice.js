import React from 'react';
import { Dropdown, message, Space, Typography, theme } from 'antd';
import { DownOutlined } from '@ant-design/icons';
import { sortByList } from '../../../js/constant'; // Import data của bạn

export default function SortByPrice({ onSortChange }) {
    const onMenuClick = ({ key }) => {
        onSortChange(key);
    };

    return (
        <Dropdown
            menu={{
                items: sortByList,
                onClick: onMenuClick
            }}
            trigger={['click']}
            getPopupContainer={() => document.body}
            overlayStyle={{ zIndex: 999999 }}
            dropdownRender={(menu) => (
                <div>
                    {menu}
                </div>
            )}
        >
            <a onClick={(e) => e.preventDefault()} style={{ cursor: 'pointer', display: 'inline-block' }}>
                <Space>
                    <Typography.Text>
                        Sort By Price <DownOutlined />
                    </Typography.Text>
                </Space>
            </a>
        </Dropdown>
    );
}