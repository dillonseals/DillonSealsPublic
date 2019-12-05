# Generated by Django 2.2.7 on 2019-12-04 07:47

from django.db import migrations, models


class Migration(migrations.Migration):

    dependencies = [
        ('eCommerce', '0022_delete_review'),
    ]

    operations = [
        migrations.CreateModel(
            name='Review',
            fields=[
                ('id', models.AutoField(auto_created=True, primary_key=True, serialize=False, verbose_name='ID')),
                ('reviewer', models.CharField(blank='true', max_length=200, null='true')),
                ('product', models.CharField(blank='true', max_length=200, null='true')),
                ('number', models.PositiveSmallIntegerField(blank='true', choices=[('1', 1), ('2', 2), ('3', 3), ('4', 4), ('5', 5)], null='true')),
                ('text', models.TextField(blank='true', null='true')),
            ],
        ),
    ]