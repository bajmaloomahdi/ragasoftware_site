import { useForm, Head } from '@inertiajs/react';
import { Button, Card, Checkbox, Form, Input, Typography } from 'antd';
import { LockOutlined, MailOutlined } from '@ant-design/icons';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit = () => post(route('login.store'));

    return (
        <div
            style={{
                minHeight: '100vh',
                display: 'grid',
                placeItems: 'center',
                background: 'linear-gradient(160deg,#0b1b34,#12294d)',
                padding: 16,
            }}
        >
            <Head title="ورود به پنل مدیریت" />
            <Card style={{ width: 380, maxWidth: '100%' }}>
                <Typography.Title level={4} style={{ textAlign: 'center', marginBottom: 24 }}>
                    ورود به پنل مدیریت
                </Typography.Title>
                <Form layout="vertical" onFinish={submit}>
                    <Form.Item
                        label="ایمیل"
                        validateStatus={errors.email ? 'error' : ''}
                        help={errors.email}
                    >
                        <Input
                            size="large"
                            prefix={<MailOutlined />}
                            value={data.email}
                            onChange={(e) => setData('email', e.target.value)}
                            autoComplete="username"
                            dir="ltr"
                        />
                    </Form.Item>
                    <Form.Item
                        label="گذرواژه"
                        validateStatus={errors.password ? 'error' : ''}
                        help={errors.password}
                    >
                        <Input.Password
                            size="large"
                            prefix={<LockOutlined />}
                            value={data.password}
                            onChange={(e) => setData('password', e.target.value)}
                            autoComplete="current-password"
                            dir="ltr"
                        />
                    </Form.Item>
                    <Form.Item>
                        <Checkbox
                            checked={data.remember}
                            onChange={(e) => setData('remember', e.target.checked)}
                        >
                            مرا به خاطر بسپار
                        </Checkbox>
                    </Form.Item>
                    <Button type="primary" htmlType="submit" size="large" block loading={processing}>
                        ورود
                    </Button>
                </Form>
            </Card>
        </div>
    );
}
