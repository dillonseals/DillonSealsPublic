# Generated by Django 2.2.7 on 2019-12-03 16:42

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0009_auto_20191203_1640'),
    ]

    operations = [
        migrations.AlterField(
            model_name='review',
            name='number',
            field=models.PositiveSmallIntegerField(blank='true', choices=[(1, 1), (2, 2), (3, 3), (4, 4), (5, 5)], null='true'),
        ),
    ]