# Generated by Django 2.2.7 on 2019-12-04 07:16

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0015_auto_20191204_0715'),
    ]

    operations = [
        migrations.AlterField(
            model_name='reviews',
            name='number',
            field=models.PositiveSmallIntegerField(blank='true', choices=[('1', 1), ('2', 2), ('3', 3), ('4', 4), ('5', 5)], null='true'),
        ),
    ]
