# Generated by Django 2.2.7 on 2019-12-04 07:35

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0019_auto_20191204_0727'),
    ]

    operations = [
        migrations.AlterField(
            model_name='product',
            name='designer',
            field=models.CharField(blank='true', max_length=100, null='true'),
        ),
        migrations.AlterField(
            model_name='product',
            name='name',
            field=models.CharField(blank='true', max_length=100, null='true'),
        ),
    ]
