# Generated by Django 2.2.7 on 2019-12-04 14:45

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0023_review'),
    ]

    operations = [
        migrations.AlterField(
            model_name='wish',
            name='user',
            field=models.CharField(blank='true', max_length=200, null='true'),
        ),
    ]